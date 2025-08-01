<?php

namespace Modules\PaymentModule\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingRepeat;
use Modules\CustomerModule\Traits\CustomerAddressTrait;
use Illuminate\Support\Facades\Validator;
use Modules\PaymentModule\Traits\PaymentHelperTrait;
use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAddress;
use Modules\PaymentModule\Traits\Payment as PaymentTrait;
use Modules\PaymentModule\Library\Payment as Payment;
use Modules\PaymentModule\Library\Payer;
use Modules\PaymentModule\Library\Receiver;


class PaymentController extends Controller
{
    use CustomerAddressTrait, PaymentHelperTrait;

    /**
     * @param Request $request
     * @return JsonResponse|Redirector|RedirectResponse|Application
     * @throws ValidationException
     */
    public function index(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        if(!is_null($request['is_add_fund']) && !$request->has('is_pay_to_admin')) {
            $validator = Validator::make($request->all(), [
                'access_token' => '',
                'amount' => 'required|numeric',
                'payment_method' => 'required|in:' . implode(',', array_column(GATEWAYS_PAYMENT_METHODS, 'key')),
                'payment_platform' => 'nullable|in:web,app'
            ]);

        }  elseif ($request->has('is_pay_to_admin')) {
            $validator_data = Validator::make($request->all(), [
                'payment_method' => 'required|in:' . implode(',', array_column(GATEWAYS_PAYMENT_METHODS, 'key')),
                'provider_id' => 'required|uuid',
            ]);

        } elseif ($request->has('is_repeat_single_booking')) {
            $validator = Validator::make($request->all(), [
                'access_token' => '',
                'payment_method' => 'required|in:' . implode(',', array_column(GATEWAYS_PAYMENT_METHODS, 'key')),
                'payment_platform' => 'nullable|in:web,app',
                'booking_repeat_id' => 'required|uuid',
            ]);
        } elseif ($request->has('switch_offline_to_digital')) {
            $validator = Validator::make($request->all(), [
                'access_token' => '',
                'payment_method' => 'required|in:' . implode(',', array_column(GATEWAYS_PAYMENT_METHODS, 'key')),
                'payment_platform' => 'nullable|in:web,app',
                'booking_id' => 'required|uuid',
            ]);
        }else {
            $serviceAtProviderPlace = (int)((business_config('service_at_provider_place', 'provider_config'))->live_values ?? 0);

            $validator = Validator::make($request->all(), [
                'access_token' => '',
                'zone_id' => 'required|uuid',
                'service_schedule' => 'required|date',
                'service_address_id' => is_null($request['service_address']) ? 'required' : 'nullable',
                'service_address' => is_null($request['service_address_id']) ? 'required' : 'nullable',
                'payment_method' => 'required|in:' . implode(',', array_column(GATEWAYS_PAYMENT_METHODS, 'key')),
                'callback' => 'nullable',
                //For bidding
                'post_id' => 'nullable|uuid',
                'provider_id' => 'nullable|uuid',
                'is_partial' => 'nullable:in:0,1',
                'payment_platform' => 'nullable|in:web,app',
                'service_location' => 'required|in:customer,provider',
                function ($attribute, $value, $fail) use ($serviceAtProviderPlace) {
                    if ($value == 'provider' && $serviceAtProviderPlace != 1) {
                        $fail('The selected service location cannot be "provider" because the service is not available at the provider’s place.');
                    }
                },
            ]);
        }

        if ($request->has('is_pay_to_admin')){
            if ($validator_data->fails()) {
                return redirect()->back()->withErrors($validator_data);
            }

            $provider = Provider::where('id', $request['provider_id'])->first();

            if ($provider){
                $amount = $provider->owner->account->account_payable - $provider->owner->account->account_receivable;
                $minPayableAmount = business_config('min_payable_amount', 'provider_config')->live_values ?? 0;
                if ($minPayableAmount > 0 && $amount < $minPayableAmount){
                    return redirect()->back()->withErrors(translate('Provider must have to pay greater than or equal to ') . $minPayableAmount);
                }
            }

        }else{
            if ($validator->fails()) {
                if ($request->has('callback')) return redirect($request['callback'] . '?flag=fail');
            else return response()->json(response_formatter(DEFAULT_400), 400);
            }
        }

        //customer user
        $customer_user_id = base64_decode($request['access_token']) ?? '';
        $is_guest = !User::where('id', $customer_user_id)->exists();
        $is_add_fund = $request['is_add_fund'] == 1 ? 1 : 0;
        $is_pay_to_admin = $request['is_pay_to_admin'] == true ? 1 : 0;
        $is_repeat_single_booking = $request['is_repeat_single_booking'] == true ? 1 : 0;
        $switch_offline_to_digital = $request['switch_offline_to_digital'] ? 1 : 0;

        //==========>>>>>> IF ADD FUND <<<<<<<==============

        //add fund
        if ($is_add_fund) {
            $customer = User::find($customer_user_id);
            $payer = new Payer($customer['first_name'] . ' ' . $customer['last_name'], $customer['email'], $customer['phone'], '');
            $payment_info = new Payment(
                success_hook: 'add_fund_success',
                failure_hook: 'add_fund_fail',
                currency_code: currency_code(),
                payment_method: $request['payment_method'],
                payment_platform: $request['payment_platform'],
                payer_id: $customer_user_id,
                receiver_id: null,
                additional_data: $validator->validated(),
                payment_amount: $request['amount'],
                external_redirect_link: $request['callback'] ?? null,
                attribute: 'booking_id',
                attribute_id: time()
            );

            $receiver_info = new Receiver('receiver_name', 'example.png');
            $redirect_link = PaymentTrait::generate_link($payer, $payment_info, $receiver_info);
            return redirect($redirect_link);
        }

        //==========>>>>>> IF Provider to Admin Pay <<<<<<<==============

        if ($is_pay_to_admin) {

            $provider = Provider::where('id', $request['provider_id'])->first();

            if ($provider) {
                $customer = User::find($provider->user_id);
                if ($provider->owner->account->account_payable > $provider->owner->account->account_receivable) {
                    $amount = $provider->owner->account->account_payable - $provider->owner->account->account_receivable;
                    $payer = new Payer($customer['first_name'] . ' ' . $customer['last_name'], $customer['email'], $customer['phone'], '');
                    $additional_data = ['provider_id'=> $request['provider_id']];

                    $payment_info = new Payment(
                        success_hook: 'pay_to_admin_success',
                        failure_hook: 'pay_to_admin_fail',
                        currency_code: currency_code(),
                        payment_method: $request['payment_method'],
                        payment_platform: 'web',
                        payer_id: $customer['id'],
                        receiver_id: null,
                        additional_data: $additional_data,
                        payment_amount: $amount,
                        external_redirect_link: route('provider.account_info'),
                        attribute: 'booking_id',
                        attribute_id: time()
                    );

                    $receiver_info = new Receiver('receiver_name', 'example.png');
                    $redirect_link = PaymentTrait::generate_link($payer, $payment_info, $receiver_info);
                    return redirect($redirect_link);
                } else {
                    return redirect()->back()->withErrors(translate('Invalid Amount'));
                }
            } else {
                return redirect()->back()->withErrors(translate('Provider Not Found'));
            }

        }

        //==========>>>>>> Repeat Single Booking Payment <<<<<<<==============

        if ($is_repeat_single_booking) {
            $repeatBooking = BookingRepeat::where('id', $request['booking_repeat_id'])->first();

            if ($repeatBooking) {
                $customer = User::find($customer_user_id);
                $amount = $repeatBooking->total_booking_amount;
                $payer = new Payer($customer['first_name'] . ' ' . $customer['last_name'], $customer['email'], $customer['phone'], '');
                $additional_data = $request->all();

                $payment_info = new Payment(
                    success_hook: 'repeat_booking_payment_success',
                    failure_hook: 'repeat_booking_payment_fail',
                    currency_code: currency_code(),
                    payment_method: $request['payment_method'],
                    payment_platform: 'web',
                    payer_id: $customer['id'],
                    receiver_id: null,
                    additional_data: $additional_data,
                    payment_amount: $amount,
                    external_redirect_link: $request['callback'] ?? null,
                    attribute: 'booking_repeat_id',
                    attribute_id: time()
                );

                $receiver_info = new Receiver('receiver_name', 'example.png');
                $redirect_link = PaymentTrait::generate_link($payer, $payment_info, $receiver_info);
                return redirect($redirect_link);
            } else {
                return redirect()->back()->withErrors(translate('Provider Not Found'));
            }

        }

        //==========>>>>>> Switch Offline to Digital Payment <<<<<<<==============

        if ($switch_offline_to_digital) {
            $booking = Booking::where('id', $request['booking_id'])->first();

            if (!isset($booking)){
                return redirect()->back()->withErrors(translate('Booking Not Found'));
            }

            $payer = new Payer('first name' . ' ' . 'last name', 'first@last.com', '1234567890', '');
            $additional_data = $request->all();

            $total_booking_amount = $booking->total_booking_amount;
            $amount_to_pay = $total_booking_amount;

            if ($request['is_partial']){
                $customer_wallet_balance = User::find($customer_user_id)?->wallet_balance;

                //partial validation
                if (!$is_guest && $request['is_partial'] && ($customer_wallet_balance <= 0 || $customer_wallet_balance >= $total_booking_amount)) {
                    return response()->json(response_formatter(DEFAULT_400), 400);
                }

                $amount_to_pay -= $customer_wallet_balance;

                $data = [
                    'wallet_paid_amount' => $customer_wallet_balance,
                    'digitally_paid_amount' => $amount_to_pay,
                ];

                $additional_data = array_merge($additional_data, $data);
            }

            $payment_info = new Payment(
                success_hook: 'switch_offline_to_digital_payment_success',
                failure_hook: 'switch_offline_to_digital_payment_fail',
                currency_code: currency_code(),
                payment_method: $request['payment_method'],
                payment_platform: 'web',
                payer_id: $customer_user_id,
                receiver_id: null,
                additional_data: $additional_data,
                payment_amount: $amount_to_pay,
                external_redirect_link: $request['callback'] ?? null,
                attribute: 'booking',
                attribute_id: time()
            );

            $receiver_info = new Receiver('receiver_name', 'example.png');
            $redirect_link = PaymentTrait::generate_link($payer, $payment_info, $receiver_info);
            return redirect($redirect_link);

        }

        //==========>>>>>> IF Booking <<<<<<<==============

        //service address create (if no saved address)
        $service_address = json_decode(base64_decode($request['service_address']));
        if (!collect($service_address)->has(['lat', 'lon', 'address', 'contact_person_name', 'contact_person_number', 'address_label'])) {
            if ($request->has('callback')) return redirect($request['callback'] . '?flag=fail');
            else return response()->json(response_formatter(DEFAULT_400), 400);
        }
        if (is_null($request['service_address_id'])) {
            $request['service_address_id'] = $this->add_address($service_address, null, $is_guest);
        }
        if (is_null($request['service_address_id'])) {
            if ($request->has('callback')) return redirect($request['callback'] . '?flag=fail');
            else return response()->json(response_formatter(DEFAULT_400), 400);
        }
        $query_params = array_merge($validator->validated(), ['service_address_id' => $request['service_address_id']]);

        // Register new user from guest info
        $newUserInfo = json_decode(base64_decode($request['new_user_info']), true);

        if($newUserInfo != null){
            $new_data = [
                'register_new_customer' => 1
            ];
            $query_params = array_merge($validator->validated(), ['service_address_id' => $request['service_address_id']], $newUserInfo, $new_data);
        }else{
            $query_params = array_merge($validator->validated(), ['service_address_id' => $request['service_address_id']]);
        }

        //guest user check
        if ($is_guest) {
            $address = UserAddress::find($request['service_address_id']);
            $customer = collect([
                'first_name' => $address['contact_person_name'],
                'last_name' => '',
                'phone' => $address['contact_person_number'],
                'email' => '',
            ]);

        } else {
            $customer = User::find($customer_user_id);
            $customer = collect([
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ]);
        }
        $query_params['customer'] = base64_encode($customer);

        $total_booking_amount = $this->find_total_Booking_amount($customer_user_id, $request['post_id'], $request['provider_id']);
        $customer_wallet_balance = User::find($customer_user_id)?->wallet_balance;
        $amount_to_pay = $request['is_partial'] ? ($total_booking_amount - $customer_wallet_balance) : $total_booking_amount;

        //partial validation
        if (!$is_guest && $request['is_partial'] && ($customer_wallet_balance <= 0 || $customer_wallet_balance >= $total_booking_amount)) {
            return response()->json(response_formatter(DEFAULT_400), 400);
        }

        //make payment
        $payer = new Payer($customer['first_name'] . ' ' . $customer['last_name'], $customer['email'], $customer['phone'], '');
        $payment_info = new Payment(
            success_hook: 'digital_payment_success',
            failure_hook: 'digital_payment_fail',
            currency_code: currency_code(),
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer_user_id,
            receiver_id: null,
            additional_data: $query_params,
            payment_amount: $amount_to_pay,
            external_redirect_link: $request['callback'] ?? null,
            attribute: 'booking_id',
            attribute_id: time()
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');
        $redirect_link = PaymentTrait::generate_link($payer, $payment_info, $receiver_info);
        return redirect($redirect_link);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Redirector|RedirectResponse|Application
     */
    public function success(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        if (isset($request['callback'])) return redirect($request['callback'] . '?flag=success');
        else return response()->json(response_formatter(DEFAULT_200), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Redirector|RedirectResponse|Application
     */
    public function fail(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        if ($request->has('callback')) return redirect($request['callback'] . '?flag=fail');
        else return response()->json(response_formatter(DEFAULT_400), 400);
    }
}

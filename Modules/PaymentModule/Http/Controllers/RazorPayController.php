<?php

namespace Modules\PaymentModule\Http\Controllers;

use App\Models\User;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Modules\PaymentModule\Traits\Processor;
use Illuminate\Contracts\Foundation\Application;
use Modules\PaymentModule\Entities\PaymentRequest;

class RazorPayController extends Controller
{
    use Processor;

    private PaymentRequest $payment;
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('razor_pay', 'payment_config');
        $razor = false;
        if (!is_null($config) && $config->mode == 'live') {
            $razor = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $razor = json_decode($config->test_values);
        }

        if ($razor) {
            $config = array(
                'api_key' => $razor->api_key,
                'api_secret' => $razor->api_secret
            );
            Config::set('razor_config', $config);
        }

        $this->payment = $payment;
        $this->user = $user;
    }

    public function index(Request $request): View|Factory|JsonResponse|Application
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        $payer = json_decode($data['payer_information']);

        if ($data['additional_data'] != null) {
            $business = json_decode($data['additional_data']);
            $business_name = $business->business_name ?? "my_business";
            $business_logo = $business->business_logo ?? url('/');
        } else {
            $business_name = "my_business";
            $business_logo = url('/');
        }

        $business_name =  (business_config('business_name', 'business_information'))->live_values ?? 'my_business';
        $business_logo = getBusinessSettingsImageFullPath(key: 'business_logo', settingType: 'business_information', path: 'business/',  defaultPath : 'public/assets/admin-module/img/placeholder.png');

        return view('paymentmodule::razor-pay', compact('data', 'payer', 'business_logo', 'business_name'));
    }

    public function payment(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $input = $request->all();
        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount'] - $payment['fee']));
            $this->payment::where(['id' => $request['payment_id']])->update([
                'payment_method' => 'razor_pay',
                'is_paid' => 1,
                'transaction_id' => $input['razorpay_payment_id'],
            ]);
            $data = $this->payment::where(['id' => $request['payment_id']])->first();
            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }
            return $this->payment_response($data, 'success');
        }
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }

    public function createOrder(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $request->validate([
            'payment_amount' => 'required|numeric',
            'currency_code' => 'required|string'
        ]);

        try {
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));

            $razorpayOrder = $api->order->create([
                'receipt' => 'order_' . uniqid(),
                'amount' => (int)(round($request['payment_amount'], 2) * 100),
                'currency' => $request['currency_code'],
                'payment_capture' => 1
            ]);

            return response()->json([
                'status' => true,
                'payment_request_id' => $request['payment_request_id'],
                'order_id' => $razorpayOrder['id'],
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency']
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function verifyPayment(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));

        // Verify payment signature
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $request['order_id'],
            'razorpay_payment_id' => $request['payment_id'],
            'razorpay_signature' => $request['signature']
        ]);

        // Fetch payment details using payment_id
        $payment = $api->payment->fetch($request['payment_id']);

        if ($payment && isset($payment['status']) && $payment['status'] == 'captured') {
            $this->payment::where(['id' => $request['payment_request_id']])->update([
                'payment_method' => 'razor_pay',
                'is_paid' => 1,
                'transaction_id' => $request['payment_id'],
            ]);
            $data = $this->payment::where(['id' => $request['payment_request_id']])->first();
            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }
            return $this->payment_response($data, 'success');
        }
        $paymentData = $this->payment::where(['id' => $request['payment_request_id']])->first();
        if (isset($paymentData) && function_exists($paymentData->failure_hook)) {
            call_user_func($paymentData->failure_hook, $paymentData);
        }
        return $this->payment_response($paymentData, 'fail');
    }

    public function callback(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $input = $request->all();
        $data_id= base64_decode($request?->payment_data);
        if (count($input) && !empty($input['razorpay_payment_id'])) {
            $data = $this->payment::where(['id' =>$data_id])->first();
            if (isset($data) && function_exists($data->success_hook)) {
                $data->payment_method=  'razor_pay';
                $data->is_paid=  1;
                $data->transaction_id= $input['razorpay_payment_id'] ;
                $data->save();
                call_user_func($data->success_hook, $data);
                return $this->payment_response($data, 'success');
            }
        }
        return redirect()->route('payment-fail');
    }
    public function cancel(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        return $this->payment_response($payment_data, 'fail');
    }
}

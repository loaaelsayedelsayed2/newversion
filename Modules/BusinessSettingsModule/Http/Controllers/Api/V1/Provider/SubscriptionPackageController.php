<?php

namespace Modules\BusinessSettingsModule\Http\Controllers\Api\V1\Provider;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;
use Illuminate\Support\Facades\Validator;
use Modules\BusinessSettingsModule\Entities\PackageSubscriber;
use Modules\BusinessSettingsModule\Entities\SubscriptionPackage;
use Modules\BusinessSettingsModule\Entities\SubscriptionPackageFeature;
use Modules\BusinessSettingsModule\Entities\SubscriptionPackageLimit;
use Modules\ProviderManagement\Entities\Provider;
use Modules\TransactionModule\Entities\Transaction;
use Modules\BusinessSettingsModule\Entities\PackageSubscriberLog;
use Modules\BusinessSettingsModule\Entities\PackageSubscriberFeature;
use Modules\BusinessSettingsModule\Entities\PackageSubscriberLimit;
use Modules\ProviderManagement\Entities\SubscribedService;
use Modules\ServiceManagement\Entities\ServiceRequest;
use Stripe\Service\SubscriptionService;

class SubscriptionPackageController extends Controller
{
    private SubscriptionPackage $subscriptionPackage;
    private PackageSubscriber $packageSubscriber;
    private  Provider $provider;
    private  Transaction $transactions;

    public function __construct(SubscriptionPackage $subscriptionPackage, PackageSubscriber $packageSubscriber, Provider $provider, Transaction $transactions)
    {
        $this->subscriptionPackage = $subscriptionPackage;
        $this->packageSubscriber = $packageSubscriber;
        $this->provider = $provider;
        $this->transactions = $transactions;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $subscriptionPackages = $this->subscriptionPackage->OfStatus(1)->with('subscriptionPackageFeature', 'subscriptionPackageLimit')->get();
        $formattedPackages = $subscriptionPackages->map(function ($subscriptionPackage) {
            return subscriptionFeatureList($subscriptionPackage, PACKAGE_FEATURES);
        });

        return response()->json(response_formatter(DEFAULT_200, $formattedPackages), 200);
    }

    /**
     * Show the form for creating a new resource.
     * @return JsonResponse
     */
    public function subscriber()
    {
        $providerId = $this->provider->where('user_id', auth()->user()->id)->value('id');
        $packageSubscriber = $this->packageSubscriber->where('provider_id', $providerId)
            ->with('feature', 'limits', 'package')
            ->first();

        if ($packageSubscriber) {
            $formattedPackage = packageSubscriber($packageSubscriber, PACKAGE_FEATURES);
            return response()->json(response_formatter(DEFAULT_200, $formattedPackage), 200);
        }

        return response()->json(response_formatter(DEFAULT_200), 200);

    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function renew(Request $request): JsonResponse
    {
        $userId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $userId)->value('id');

        $package = $this->subscriptionPackage->where('id', $request->package_id)->ofStatus(1)->first();

        if (!$package) {
            return response()->json(response_formatter(DEFAULT_400), 400);
        }

        $id                 = $package->id;
        $price              = $package->price;
        $name               = $package->name;
        $vatPercentage      = (int)((business_config('subscription_vat', 'subscription_Setting'))->live_values ?? 0);
        $vatAmount          = $package->price * ($vatPercentage / 100);
        $vatWithPrice       = $price + $vatAmount;

        $subscriber = $this->packageSubscriber->where('subscription_package_id', $id)->where('provider_id', $providerId)->first();
        if ($subscriber) {
            $paymentUrl = url('payment/subscription') . '?' .
                'provider_id=' . $providerId . '&' .
                'access_token=' . base64_encode($userId) . '&' .
                'amount=' . $vatWithPrice . '&' .
                'name=' . $name . '&' .
                'package_status=' . 'subscription_renew' . '&' .
                http_build_query($request->all());

            return response()->json(response_formatter(DEFAULT_200, $paymentUrl), 200);
        }

        return response()->json(response_formatter(DEFAULT_400), 400);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function shift(Request $request): JsonResponse
    {
        $userId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $userId)->value('id');

        $package = $this->subscriptionPackage->where('id', $request->package_id)->ofStatus(1)->first();
        if (!$package) {
            return response()->json(response_formatter(DEFAULT_400), 400);
        }

        $id                 = $package->id;
        $price              = $package->price;
        $name               = $package->name;
        $vatPercentage      = (int)((business_config('subscription_vat', 'subscription_Setting'))->live_values ?? 0);
        $vatAmount          = $package->price * ($vatPercentage / 100);
        $vatWithPrice       = $price + $vatAmount;

        $paymentUrl = url('payment/subscription') . '?' .
            'provider_id=' . $providerId . '&' .
            'access_token=' . base64_encode($userId) . '&' .
            'amount=' . $vatWithPrice . '&' .
            'name=' . $name . '&' .
            'package_status=' . 'subscription_shift' . '&' .
            http_build_query($request->all());

        return response()->json(response_formatter(DEFAULT_200, $paymentUrl), 200);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function purchase(Request $request): JsonResponse
    {
        $userId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $userId)->value('id');

        $package = $this->subscriptionPackage->where('id', $request->package_id)->ofStatus(1)->first();
        if (!$package) {
            return response()->json(response_formatter(DEFAULT_400), 400);
        }

        $price              = $package->price;
        $name               = $package->name;
        $vatPercentage      = (int)((business_config('subscription_vat', 'subscription_Setting'))->live_values ?? 0);
        $vatAmount          = $package->price * ($vatPercentage / 100);
        $vatWithPrice       = $price + $vatAmount;

        $paymentUrl = url('payment/subscription') . '?' .
            'provider_id=' . $providerId . '&' .
            'access_token=' . base64_encode($userId) . '&' .
            'amount=' . $vatWithPrice . '&' .
            'name=' . $name . '&' .
            'package_status=' . 'business_plan_change' . '&' .
            http_build_query($request->all());

        return response()->json(response_formatter(DEFAULT_200, $paymentUrl), 200);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function commission(Request $request): JsonResponse
    {
        $roundedPercentageUsed = 0;
        $totalDuration = 0;
        $userId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $userId)->value('id');

        $subscriber = $this->packageSubscriber->where('provider_id', $providerId)->with('logs')->first();
        $usedTime   = (int)((business_config('usage_time', 'subscription_Setting'))?->live_values ?? 0);

        if (!$subscriber) {
            return response()->json(response_formatter(ALREADY_COMMISSION_BASE), 400);
        }

        $packageStartDate = Carbon::parse($subscriber->package_start_date);
        $packageEndDate = Carbon::parse($subscriber->package_end_date);
        $now = Carbon::now();

        if ($subscriber?->payment?->is_paid) {
            $totalDuration = $packageStartDate->diffInDays($packageEndDate);
            $daysPassed = $packageStartDate->diffInDays($now);
            $percentageUsed = 0;
            if ($totalDuration != 0) {
                $percentageUsed = ($daysPassed / $totalDuration) * 100;
            }
            $roundedPercentageUsed = ceil($percentageUsed);
        }

        if ($usedTime > $roundedPercentageUsed && $totalDuration != 0) {
            shiftRefundSubscriptionTransaction(
                provider_id: $providerId
            );
        }
        $subscriber->delete();

        return response()->json(response_formatter(SHIFT_SUBSCRIPTION_PACKAGE), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        $packageId = $request->package_id;
        $userId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $userId)->value('id');
        $package = $this->packageSubscriber->where('subscription_package_id', $packageId)->where('provider_id', $providerId)->first();
        if ($package) {
            $package->is_canceled = 1;
            $package->payment_id = $request->payment_id;
            $package->save();

            return response()->json(response_formatter(DEFAULT_200), 200);
        }
        return response()->json(response_formatter(DEFAULT_400), 400);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function transactions(Request $request): JsonResponse
    {
        $search = $request->has('search') ? $request->get('search') : '';
        $startDate = $request->has('start_date') ? $request->get('start_date') : null;
        $endDate = $request->has('end_date') ? $request->get('end_date') : null;

        $queryParams = ['search' => $search];
        $providerUserId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $providerUserId)->value('id');
        if (!$providerId) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        $transactions = $this->transactions
            ->where(function ($q) use ($providerUserId) {
                $q->where('from_user_id', $providerUserId)
                    ->orWhere('to_user_id', $providerUserId);
            })
            ->when($search, function ($query) use ($search) {
                $keys = explode(' ', $search);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('id', 'LIKE', '%' . $key . '%')
                            ->orWhereHas('packageLog.payment', function ($query) use ($key) {
                                $query->where('transaction_id', 'LIKE', '%' . $key . '%');
                            });
                    }
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate = Carbon::parse($endDate)->endOfDay();
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('packageLog.payment', 'packageLog.provider')
            ->whereIn('trx_type', ['subscription_purchase', 'subscription_renew', 'subscription_shift', 'subscription_refund'])
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])
            ->withPath('')
            ->appends($queryParams);

        return response()->json(response_formatter(DEFAULT_200, $transactions), 200);
    }


    public function newSubscription(Request $request)
    {
        DB::beginTransaction();
        try {
            $provider = auth('api')->user()->provider;

            $validator = Validator::make($request->all(), [
                'package_subscription_id' => 'required',
                'status' => 'required|in:success,failed',
                'payment_id' => 'required',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json(response_formatter(DEFAULT_400, $validator->errors()), 400);
            }

            $packageSubscriber = PackageSubscriber::where('subscription_package_id', $request->package_subscription_id)
                ->where('provider_id', $provider->id)->first();


            if (!$packageSubscriber) {
                DB::rollBack();
                return response()->json(response_formatter(DEFAULT_400, 'Invalid package subscription'), 400);
            }

            $package = SubscriptionPackage::find($request->package_subscription_id);
            if (!$package) {
                DB::rollBack();
                return response()->json(response_formatter(DEFAULT_404, 'Package not found'), 404);
            }
            if ($request->status == 'success') {
                $duration = $package->duration;
                $startDate = Carbon::now()->startOfDay();
                // $endDate = Carbon::now()->addDays($duration)->subDay();
                $endDate = Carbon::now()->addDays($duration);
                $packageSubscriber->package_start_date = $startDate;
                $packageSubscriber->package_end_date = $endDate ;
                $packageSubscriber->trial_duration = 0;
                $packageSubscriber->payment_id = $request->payment_id;
                $packageSubscriber->payment_method = 'Moyasar';
                $packageSubscriber->is_notified = 1;

                if ($packageSubscriber->is_canceled == 1) {
                    $packageSubscriber->is_canceled = 0;
                }

                $packageSubscriber->save();

                $vatPercentage = (int)(business_config('subscription_vat', 'subscription_Setting')->live_values ?? 0);
                $calculationVat = $package->price * ($vatPercentage / 100);

                $transactionId = renewSubscriptionTransaction(
                    amount: $package->price,
                    provider_id: $provider->id,
                    vat: $calculationVat
                );

                $packageSubscriberLog = new PackageSubscriberLog();
                $packageSubscriberLog->end_date = $endDate;
                $packageSubscriberLog->start_date = $startDate;
                $packageSubscriberLog->vat_amount = $calculationVat;
                $packageSubscriberLog->payment_id = $request->payment_id;
                $packageSubscriberLog->provider_id = $provider->id;
                $packageSubscriberLog->package_name = $package->name;
                $packageSubscriberLog->package_price = $package->price;
                $packageSubscriberLog->vat_percentage = $vatPercentage;
                $packageSubscriberLog->subscription_package_id = $package->id;
                $packageSubscriberLog->primary_transaction_id = $transactionId;
                $packageSubscriberLog->payment_method = 'Moyasar';
                $packageSubscriberLog->save();

                $packageSubscriber->package_subscriber_log_id = $packageSubscriberLog->id;
                $packageSubscriber->save();

                $features = SubscriptionPackageFeature::where('subscription_package_id', $package->id)->get();
                foreach ($features as $feature) {
                    $packageSubscriberFeature = new PackageSubscriberFeature();
                    $packageSubscriberFeature->provider_id = $provider->id;
                    $packageSubscriberFeature->package_subscriber_log_id = $packageSubscriberLog->id;
                    $packageSubscriberFeature->feature = $feature->feature;
                    $packageSubscriberFeature->save();
                }


                DB::commit();
                return response()->json(response_formatter(DEFAULT_200, $features), 200);
            } else {
                DB::rollBack();
                return response()->json(response_formatter(DEFAULT_400, 'Subscription Failed'), 400);
            }
        } catch (\Exception $e) {  // Fixed typo in Exception
            DB::rollBack();
            return response()->json(response_formatter(DEFAULT_400, $e->getMessage()), 500);
        }
    }





    public function convertSubscription(Request $request)
    {
        $provider = auth('api')->user()->provider;

        $validator = Validator::make($request->all(), [
            'package_subscription_id' => 'required',
            'status' => 'required|in:success,failed',
            'payment_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, $validator->errors()), 400);
        }

        if ($request->status == 'success') {
            $package = SubscriptionPackage::with('subscriptionPackageFeature')->find($request->package_subscription_id);
            if (!$package) {
                return response()->json(response_formatter(DEFAULT_400, 'Invalid new package subscription'), 400);
            }
            $duration = $package->duration;

            $packageSubscriber = PackageSubscriber::with('feature')->where('provider_id', $provider->id)->first();
            $addLog = new PackageSubscriberLog();
            $addLog->provider_id  =  $provider->id;
            $addLog->subscription_package_id =  $package->id;
            $addLog->package_name =  $package->name;
            $addLog->package_price =  $package->price;
            $addLog->start_date = Carbon::now();
            // $addLog->end_date =  Carbon::now()->addDays($duration)->subDay();
            $addLog->end_date =  Carbon::now()->addDays($duration);
            $vatPercentage = (int)(business_config('subscription_vat', 'subscription_Setting')->live_values ?? 0);
            $calculationVat = $package->price * ($vatPercentage / 100);
            $transactionId = shiftSubscriptionTransaction(
                amount: $package->price,
                provider_id: $provider->id,
                vat: $calculationVat
            );
            $addLog->primary_transaction_id = $transactionId;
            $addLog->payment_id = $request->payment_id;
            $addLog->payment_method = 'Moyasar';
            $addLog->save();

            if ($packageSubscriber != null) {
                $packageSubscriber->subscription_package_id = $request->package_subscription_id;
                $packageSubscriber->package_name = $package->name;
                $packageSubscriber->package_price = $package->price;
                $packageSubscriber->package_start_date = Carbon::now();
                $packageSubscriber->package_end_date = Carbon::now()->addDays($duration);
                // $packageSubscriber->package_end_date = Carbon::now()->addDays($duration)->subDay();
                $packageSubscriber->trial_duration = 0;
                $packageSubscriber->payment_method = 'Moyasar';
                $packageSubscriber->is_notified = 1;
                $packageSubscriber->payment_id = $request->payment_id;
                if ($packageSubscriber->is_canceled == 1) {
                    $packageSubscriber->is_canceled = 0;
                }
                $packageSubscriber->package_subscriber_log_id = $addLog->id;
                $packageSubscriber->save();
            } else {
                $packageSubscriber = new PackageSubscriber();
                $packageSubscriber->provider_id = $provider->id;
                $packageSubscriber->subscription_package_id = $request->package_subscription_id;
                $packageSubscriber->package_name = $package->name;
                $packageSubscriber->package_price = $package->price;
                $packageSubscriber->package_start_date = Carbon::now();
                $packageSubscriber->package_end_date = Carbon::now()->addDays($duration);
                // $packageSubscriber->package_end_date = Carbon::now()->addDays($duration)->subDay();
                $packageSubscriber->trial_duration = 0;
                $packageSubscriber->package_subscriber_log_id = $addLog->id;
                $packageSubscriber->is_notified = 1;
                $packageSubscriber->payment_method = 'Moyasar';
                $packageSubscriber->payment_id = $request->payment_id;
                $packageSubscriber->save();
            }

            PackageSubscriberLimit::where('provider_id', $provider->id)->delete();

            $limits = SubscriptionPackageLimit::where('subscription_package_id', $package->id)->get();
            foreach ($limits as $limit) {
                $limitPackage = new PackageSubscriberLimit();
                $limitPackage->provider_id = $provider->id;
                $limitPackage->subscription_package_id = $package->id;
                $limitPackage->key = $limit->key;
                $limitPackage->is_limited = $limit->is_limited;
                $limitPackage->limit_count = $limit->limit_count;
                $limitPackage->save();
            }

            PackageSubscriberFeature::where('provider_id', $provider->id)->delete();

            $features = SubscriptionPackageFeature::where('subscription_package_id', $package->id)->get();
            foreach ($features as $feature) {
                $packageSubscriberFeature = new PackageSubscriberFeature();
                $packageSubscriberFeature->provider_id = $provider->id;
                $packageSubscriberFeature->package_subscriber_log_id = $addLog->id;
                $packageSubscriberFeature->feature = $feature->feature;
                $packageSubscriberFeature->save();
            }

            $this->closeSubscriptionService();

            return response()->json(response_formatter(DEFAULT_200, 'Subscription successfully updated to new package'), 200);
        } else {
            return response()->json(response_formatter(DEFAULT_400, 'Subscription creation failed'), 400);
        }
    }


    public function closeSubscriptionService(){
        $userId = auth('api')->user()->id;
        $providerId = $this->provider::where('user_id', $userId)->value('id');
        $subscribedService = SubscribedService::where('provider_id', $providerId)->where('is_subscribed', 1)->get();
        if ($subscribedService) {
            foreach ($subscribedService as $service) {
                $service->is_subscribed = 0;
                $service->save();
            }
            return response()->json(response_formatter(DEFAULT_200, 'Subscription service removed successfully'), 200);
        } else {
            return response()->json(response_formatter(DEFAULT_400, 'No subscription service found'), 400);
        }
    }
}

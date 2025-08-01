<?php

namespace Modules\CustomerModule\Http\Controllers\Api\V1\Customer;

use App\Traits\MaintenanceModeTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\BusinessSettingsModule\Entities\DataSetting;
use Modules\BusinessSettingsModule\Entities\ErrorLog;
use Modules\BusinessSettingsModule\Entities\LoginSetup;
use Modules\PaymentModule\Entities\Setting;
use Modules\ServiceManagement\Entities\Service;
use Modules\UserManagement\Entities\User;
use Modules\ZoneManagement\Entities\Zone;
use Stevebauman\Location\Facades\Location;

class ConfigController extends Controller
{
    use MaintenanceModeTrait;

    private $googleMap;
    private $googleMapBaseApi;

    function __construct()
    {
        $this->googleMap = business_config('google_map', 'third_party');
        $this->googleMapBaseApi = 'https://maps.googleapis.com/maps/api';
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function configuration(Request $request): JsonResponse
    {
        $location = Location::get($request->ip());

        $playstore = business_config('app_url_playstore', 'landing_button_and_links');
        $appstore = business_config('app_url_appstore', 'landing_button_and_links');

        $socialMediaSettings = login_setup('social_media_for_login');
        $getValue = json_decode($socialMediaSettings['value'], true);
        $googleSocialLogin = $getValue['google'];
        $facebookSocialLogin = $getValue['facebook'];
        $appleSocialLogin = $getValue['apple'];

        $advancedBooking = [
            'advanced_booking_restriction_value' => (int)business_config('advanced_booking_restriction_value', 'booking_setup')?->live_values,
            'advanced_booking_restriction_type' => business_config('advanced_booking_restriction_type', 'booking_setup')?->live_values,
        ];

        //payment gateways
        $isPublished = 0;
        try {
            $full_data = include('Modules/Gateways/Addon/info.php');
            $isPublished = $full_data['is_published'] == 1 ? 1 : 0;
        } catch (\Exception $exception) {
        }

        $payment_gateways = collect($this->getPaymentMethods())
            ->filter(function ($query) use ($isPublished) {
                if (!$isPublished) {
                    return in_array($query['gateway'], array_column(PAYMENT_METHODS, 'key'));
                } else return $query;
            })->map(function ($query) {
                $query['label'] = ucwords(str_replace('_', ' ', $query['gateway_title']));
                return $query;
            })->values();

        $countryData = business_config('system_language', 'business_information')?->live_values;

        $country = [];

        foreach ($countryData as $item) {
            if ($item['status'] == 1) {
                $country[] = $item;
            }
        }

        $errorLogs = ErrorLog::where('redirect_url', '!=', null)->get();

        $location = Location::get($request->ip());

        $loginOptionsValue = LoginSetup::where(['key' => 'login_options'])?->first()?->value;
        $loginOptions = json_decode($loginOptionsValue);

        $socialMediaLoginValue = LoginSetup::where(['key' => 'social_media_for_login'])?->first()?->value;
        $socialMediaLoginOptions = json_decode($socialMediaLoginValue);


        $customerLogin = [
            'login_option' => $loginOptions,
            'social_media_login_options' => $socialMediaLoginOptions
        ];

        $dataValues = Setting::where('settings_type', 'sms_config')->get();
        $count = 0;
        foreach ($dataValues as $gateway) {
            $status = $gateway?->live_values['status'] ?? 0;
            if ($status == 1) {
                $count = 1;
            }
        }
        $emailConfig = business_config('email_config_status', 'email_config')?->live_values;
        $firebaseOtpConfig = business_config('firebase_otp_verification', 'third_party');
        $firebaseOtpStatus = (int)$firebaseOtpConfig?->live_values['status'] ?? null;

        if ($firebaseOtpStatus == 1) {
            $count = 1;
        }

        $forgotPasswordVerificationMethod = [
            'phone' => $count,
            'email' => $emailConfig
        ];

        return response()->json(response_formatter(DEFAULT_200, [
            'default_location' => [
                'latitude' => data_get($location, 'latitude', ''),
                'longitude' => data_get($location, 'longitude', '')
            ],
            'maintenance' => $this->checkMaintenanceMode(),
            'business_name' => (business_config('business_name', 'business_information'))->live_values ?? null,
            'logo_full_path' => getBusinessSettingsImageFullPath(key: 'business_logo', settingType: 'business_information', path: 'business/', defaultPath: 'public/assets/admin-module/img/media/banner-upload-file.png'),
            'favicon_full_path' => getBusinessSettingsImageFullPath(key: 'business_favicon', settingType: 'business_information', path: 'business/', defaultPath: 'public/assets/admin-module/img/media/upload-file.png'),
            'country_code' => (business_config('country_code', 'business_information'))->live_values ?? null,
            'business_address' => (business_config('business_address', 'business_information'))->live_values ?? null,
            'business_phone' => (business_config('business_phone', 'business_information'))->live_values ?? null,
            'business_email' => (business_config('business_email', 'business_information'))->live_values ?? null,
            'base_url' => rtrim(url('/'), '/') . '/api/v1/',
            'currency_decimal_point' => (business_config('currency_decimal_point', 'business_information'))->live_values ?? null,
            'currency_code' => (business_config('currency_code', 'business_information'))->live_values ?? null,
            'currency_symbol' => currency_symbol() ?? '',
            'currency_symbol_position' => (business_config('currency_symbol_position', 'business_information'))->live_values ?? null,
            'about_us' => route('about-us'),
            'privacy_policy' => route('privacy-policy'),
            'terms_and_conditions' =>  route('terms-and-conditions'),
            'refund_policy' => DataSetting::where('key', 'refund_policy')->first()->is_active == '1' ? route('refund-policy') : "",
            'cancellation_policy' => DataSetting::where('key', 'cancellation_policy')->first()->is_active == '1' ? route('cancellation-policy') : "",
            'pagination_limit' => (int)pagination_limit(),
            'time_format' => (business_config('time_format', 'business_information'))->live_values ?? '24h',
            'payment_gateways' => $payment_gateways,
            'footer_text' => (business_config('footer_text', 'business_information'))->live_values ?? null,
            'cookies_text' => (business_config('cookies_text', 'business_information'))->live_values ?? null,
            'admin_details' => User::select('id', 'first_name', 'last_name', 'profile_image')->where('user_type', ADMIN_USER_TYPES[0])->first(),
            'min_versions' => json_decode((business_config('customer_app_settings', 'app_settings'))->live_values ?? null),
            'app_url_playstore' => $playstore->is_active ? $playstore->live_values : null,
            'app_url_appstore' => $appstore->is_active ? $appstore->live_values : null,
            'web_url' => (business_config('web_url', 'landing_button_and_links'))->is_active == '1' ? (business_config('web_url', 'landing_button_and_links'))->live_values : null,
            'google_social_login' => (int)($googleSocialLogin ?? 0),
            'facebook_social_login' => (int)($facebookSocialLogin ?? 0),
            'apple_social_login' => (int)($appleSocialLogin ?? 0),
            'phone_number_visibility_for_chatting' => (int)((business_config('phone_number_visibility_for_chatting', 'business_information'))->live_values ?? 0),
            'wallet_status' => (int)((business_config('customer_wallet', 'customer_config'))->live_values ?? 0),
            'add_to_fund_wallet' => (int)((business_config('add_to_fund_wallet', 'customer_config'))->live_values ?? 0),
            'loyalty_point_status' => (int)((business_config('customer_loyalty_point', 'customer_config'))->live_values ?? 0),
            'referral_earning_status' => (int)((business_config('customer_referral_earning', 'customer_config'))->live_values ?? 0),
            'direct_provider_booking' => (int)((business_config('direct_provider_booking', 'business_information'))->live_values ?? 0),
            'bidding_status' => (int)((business_config('bidding_status', 'bidding_system'))->live_values ?? 0),
            'phone_verification' => (int)((login_setup('phone_verification'))->value ?? 0),
            'email_verification' => (int)((login_setup('email_verification'))->value ?? 0),
            'cash_after_service' => (int)((business_config('cash_after_service', 'service_setup'))->live_values ?? 0),
            'digital_payment' => (int)((business_config('digital_payment', 'service_setup'))->live_values ?? 0),
            'wallet_payment' => (int)((business_config('wallet_payment', 'service_setup'))->live_values ?? 0),
            'social_media' => (business_config('social_media', 'landing_social_media'))->live_values ?? null,
            'otp_resend_time' => (int)(business_config('otp_resend_time', 'otp_login_setup'))?->live_values ?? null,
            'max_booking_amount' => (float)(business_config('max_booking_amount', 'booking_setup'))?->live_values ?? null,
            'min_booking_amount' => (float)(business_config('min_booking_amount', 'booking_setup'))?->live_values ?? null,
            'guest_checkout' => (int)(business_config('guest_checkout', 'service_setup'))?->live_values ?? null,
            'partial_payment' => (int)(business_config('partial_payment', 'service_setup'))?->live_values ?? null,
            'booking_additional_charge' => (int)(business_config('booking_additional_charge', 'booking_setup'))?->live_values ?? null,
            'additional_charge_label_name' => (string)(business_config('additional_charge_label_name', 'booking_setup'))?->live_values ?? null,
            'additional_charge_fee_amount' => (float)(business_config('additional_charge_fee_amount', 'booking_setup'))?->live_values ?? null,
            'offline_payment' => (int)(business_config('offline_payment', 'service_setup'))?->live_values ?? null,
            'partial_payment_combinator' => (string)(business_config('partial_payment_combinator', 'service_setup'))?->live_values ?? null,
            'provider_self_registration' => (int)business_config('provider_self_registration', 'provider_config')?->live_values,
            'confirm_otp_for_complete_service' => (int)business_config('booking_otp', 'booking_setup')?->live_values,
            'instant_booking' => (int)business_config('instant_booking', 'booking_setup')?->live_values,
            'schedule_booking' => (int)business_config('schedule_booking', 'booking_setup')?->live_values,
            'schedule_booking_time_restriction' => (int)business_config('schedule_booking_time_restriction', 'booking_setup')?->live_values,
            'advanced_booking' => $advancedBooking,
            'system_language' => $country,
            'login_setup' => $customerLogin,
            'firebase_otp_verification' => $firebaseOtpStatus,
            'forgot_password_verification_method' => $forgotPasswordVerificationMethod,
            'error_logs' => $errorLogs,
            'app_environment' => env('APP_ENV'),
            'repeat_booking' => (int)business_config('repeat_booking', 'booking_setup')?->live_values,
            'create_user_account_from_guest_info' => (int)(business_config('create_user_account_from_guest_info', 'business_information'))?->live_values ?? 0,
            'service_at_provider_place' => (int)((business_config('service_at_provider_place', 'provider_config'))->live_values ?? 0),
            'newsletter_title' => DataSetting::where('type', 'landing_text_setup')->where('key', 'newsletter_title')->first()->value ?? '',
            'newsletter_description' => DataSetting::where('type', 'landing_text_setup')->where('key', 'newsletter_description')->first()->value ?? '',

        ]), 200);
    }

    public function pages(): JsonResponse
    {
        return response()->json(response_formatter(DEFAULT_200, [
            'about_us' => DataSetting::where('type', 'pages_setup')->where('key', 'about_us')->first(),
            'terms_and_conditions' => DataSetting::where('type', 'pages_setup')->where('key', 'terms_and_conditions')->first(),
            'refund_policy' => DataSetting::where('type', 'pages_setup')->where('key', 'refund_policy')->first(),
            'return_policy' => DataSetting::where('type', 'pages_setup')->where('key', 'return_policy')->first(),
            'cancellation_policy' => DataSetting::where('type', 'pages_setup')->where('key', 'cancellation_policy')->first(),
            'privacy_policy' => DataSetting::where('type', 'pages_setup')->where('key', 'privacy_policy')->first(),
            'images' => collect([
                'about_us' => getDataSettingsImageFullPath(key: 'about_us_image', settingType: 'pages_setup_image', path: 'page-setup/', defaultPath: asset('public/assets/admin-module/img/page-default.png')),
                'terms_and_conditions' => getDataSettingsImageFullPath(key: 'terms_and_conditions_image', settingType: 'pages_setup_image', path: 'page-setup/', defaultPath: asset('public/assets/admin-module/img/page-default.png')),
                'refund_policy' => getDataSettingsImageFullPath(key: 'refund_policy_image', settingType: 'pages_setup_image', path: 'page-setup/', defaultPath: asset('public/assets/admin-module/img/page-default.png')),
                'return_policy' => getDataSettingsImageFullPath(key: 'return_policy_image', settingType: 'pages_setup_image', path: 'page-setup/', defaultPath: asset('public/assets/admin-module/img/page-default.png')),
                'cancellation_policy' => getDataSettingsImageFullPath(key: 'cancellation_policy_image', settingType: 'pages_setup_image', path: 'page-setup/', defaultPath: asset('public/assets/admin-module/img/page-default.png')),
                'privacy_policy' => getDataSettingsImageFullPath(key: 'privacy_policy_image', settingType: 'pages_setup_image', path: 'page-setup/', defaultPath: asset('public/assets/admin-module/img/page-default.png')),
            ])
        ]), 200);
    }

    public function getZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $point = new Point($request->lat, $request->lng);
        $zone = Zone::whereContains('coordinates', $point)->ofStatus(1)->latest()->first();

        if ($zone) {
            $zone['formatted_coordinates'] = formatCoordinates($zone->coordinates);

            $services = Service::withoutGlobalScope('zone_wise_data')->where('is_active', 1)->whereHas('category', function ($query) use ($zone) {
                $query->OfStatus(1)->withoutGlobalScope('zone_wise_data')->whereHas('zones', function ($query) use ($zone) {
                    $query->where('zone_id', $zone->id);
                });
            })->count();

            return response()->json(response_formatter(DEFAULT_200, [
                'zone' => $zone,
                'available_services_count' => $services,
            ]), 200);
        }

        return response()->json(response_formatter(ZONE_RESOURCE_404), 200);
    }

    public function placeApiAutocomplete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $url = 'https://places.googleapis.com/v1/places:autocomplete';

        $data = [
            'input' => $request->input('search_text'),
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $this->googleMap->live_values['map_api_key_server'],
            'X-Goog-FieldMask' => '*'
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        return response()->json(response_formatter(DEFAULT_200, $response->json()), 200);
    }

    public function distanceApi(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $url = 'https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix';

        $origin = [
            "waypoint" => [
                "location" => [
                    "latLng" => [
                        "latitude" =>  $request['origin_lat'],
                        "longitude" => $request['origin_lng']
                    ]
                ]
            ]
        ];

        $destination = [
            "waypoint" => [
                "location" => [
                    "latLng" => [
                        "latitude" => $request['destination_lat'],
                        "longitude" => $request['destination_lng']
                    ]
                ]
            ]
        ];

        $data = [
            "origins" => $origin,
            "destinations" => $destination,
            "travelMode" => "DRIVE",
            "routingPreference" => "TRAFFIC_AWARE"
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $this->googleMap->live_values['map_api_key_server'],
            'X-Goog-FieldMask' => '*'
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        return response()->json(response_formatter(DEFAULT_200, $response->json()), 200);
    }

    public function placeApiDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'placeid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $url = 'https://places.googleapis.com/v1/places/'.  $request['placeid'];

        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $this->googleMap->live_values['map_api_key_server'],
            'X-Goog-FieldMask' => '*'
        ];

        $response = Http::withHeaders($headers)->get($url);

        return response()->json(response_formatter(DEFAULT_200, $response->json()), 200);
    }

    public function geocodeApi(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->lat . ',' . $request->lng . '&key=' . $this->googleMap->live_values['map_api_key_server']);
        return response()->json(response_formatter(DEFAULT_200, $response->json()), 200);
    }

    private function getPaymentMethods(): array
    {
        // Check if the addon_settings table exists
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')->where('settings_type', 'payment_config')->get();
        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $gateway_image = getPaymentGatewayImageFullPath(key: $method->key_name, settingsType: $method->settings_type, defaultPath: null);
            $credentialsData = json_decode($method->$credentials);
            $additional_data = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_image_full_path' => $gateway_image,
                    'gateway_title' => $additional_data->gateway_title,
                ];
            }
        }
        return $data;
    }

}

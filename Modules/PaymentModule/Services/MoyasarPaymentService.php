<?php

namespace Modules\PaymentModule\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\PaymentModule\Interfaces\PaymentGatewayInterface;

// class MoyasarPaymentService extends BasePaymentService implements PaymentGatewayInterface
// {
//     /**
//      * Create a new class instance.
//      */
//     protected  $api_secrat;

//     public function __construct()
//     {
//         $this->base_url = env('MOYASAR_BASE_URL');
//         $this->api_secrat = env('MOYASAR_SECRET_KEY');
//         $this->header = [
//             "accept" => "application/json",
//             "Content-Type" => "application/json",
//             "Authorization" => "Basic ". base64_encode($this->api_secrat . ':')

//         ];
//     }

//     public function sendPayment(Request $request){
//         $data = $request->all();
//         $userId = auth()->id();
//         if (!$userId) {
//             return [
//                 'success' => false,
//                 'message' => 'User not authenticated'
//             ];
//         }
//             $data['success_url'] = $request->getSchemeAndHttpHost() . '/api/v1/payment/callback?' .
//             http_build_query([
//                 'booking_id' => $data['booking_id'],
//                 'user_id' => $userId,
//                 "redirect_url" => $data['redirect_url'],
//                 'status' => 'success'
//             ]);
//             $data['failure_url'] = $request->getSchemeAndHttpHost() . '/api/v1/payment/callback?' .
//             http_build_query([
//                 'booking_id' => $data['booking_id'],
//                 'user_id' => $userId,
//                 "redirect_url" => $data['redirect_url'],
//                 'status' => 'failure'
//             ]);

//         $response = $this->buildRequest('POST','/v1/invoices',$data);
//         if($response->getData(true)['success']){
//             return[
//                 'success' => true,
//                 'url' =>$response->getData(true)['data']['url']
//             ];
//         }
//         return[
//             'success' => false,
//             'url' =>$response
//         ];
//     }

//     public function callBack(Request $request)
//     {
//         $response_status = $request->get('status');
//         Storage::put('Moyasar/response_'.time().'.json', json_encode($request->all()));
//         if(isset($response_status) && strtolower($response_status) == "paid") {
//             return true;
//         }
//         return false;
//     }
// }

class MoyasarPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    protected $api_secret;

    public function __construct()
    {
        $this->base_url = env('MOYASAR_BASE_URL', 'https://api.moyasar.com');
        $this->api_secret = env('MOYASAR_SECRET_KEY');
        $this->header = [
            "accept" => "application/json",
            "Content-Type" => "application/json",
            "Authorization" => "Basic ". base64_encode($this->api_secret . ':')
        ];
    }

    public function sendPayment(Request $request)
    {
        $data = $request->all();
        $userId = auth()->id();

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'User not authenticated'
            ];
        }

        // بناء روابط الاستدعاء العكسي بشكل صحيح
        $callbackParams = [
            'booking_id' => $data['booking_id'],
            'user_id' => $userId,
            'redirect_url' => $data['redirect_url'] ?? null
        ];

        $paymentData = [
            'amount' => $data['amount'] * 100, // تحويل المبلغ إلى هللات
            'currency' => $data['currency'] ?? 'SAR',
            'description' => $data['description'] ?? 'Booking Payment',
            'callback_url' => $request->getSchemeAndHttpHost() . '/api/v1/payment/callback',
            'metadata' => $callbackParams
        ];

        $response = $this->buildRequest('POST', '/v1/payments', $paymentData);

        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'success' => true,
                'url' => $responseData['source']['transaction_url'] ?? $responseData['source']['redirect_url']
            ];
        }

        return [
            'success' => false,
            'message' => $response->json()['message'] ?? 'Payment initiation failed'
        ];
    }

    public function callBack(Request $request)
    {
        Storage::put('Moyasar/response_'.time().'.json', json_encode($request->all()));

        // التحقق من حالة الدفع حسب وثائق Moyasar الرسمية
        if ($request->has('id')) {
            $paymentId = $request->input('id');
            $paymentResponse = $this->buildRequest('GET', "/v1/payments/{$paymentId}");

            if ($paymentResponse->successful()) {
                $paymentData = $paymentResponse->json();
                return $paymentData['status'] === 'paid';
            }
        }

        return false;
    }
}

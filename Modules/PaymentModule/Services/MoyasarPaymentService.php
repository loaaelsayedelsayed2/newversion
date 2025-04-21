<?php

namespace Modules\PaymentModule\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\PaymentModule\Interfaces\PaymentGatewayInterface;

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
            "Authorization" => "Basic " . base64_encode($this->api_secret . ':')
        ];
    }

    public function sendPayment(Request $request)
    {
        try {
            $data = $request->all();
            $userId = auth()->id();

            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            $callbackParams = [
                'booking_id' => $data['booking_id'],
                'user_id' => $userId,
                'redirect_url' => $data['redirect_url'] ?? null,
            ];
            http: //127.0.0.1:8000/api/v1/payment/callback?id=5231c64f-83e0-434a-99d2-f8020a74647b&status=paid&message=APPROVED&invoice_id=8668bbd2-66a6-477d-8f12-ded0feb4bce7&booking_id=30d11086-2643-4a71-af9e-9c8a6372947e&user_id=031f002d-837a-49eb-97a8-93f8cb0e7083&redirect_url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3Dtpcq6jQSJYU
            $data['success_url'] = $request->getSchemeAndHttpHost() . '/api/v1/payment/callback?'
                . http_build_query(array_merge($callbackParams));

            $data['failure_url'] = $request->getSchemeAndHttpHost() . '/api/v1/payment/callback?'
                . http_build_query(array_merge($callbackParams));

            $response = $this->buildRequest('POST', '/v1/invoices', $data);
            $responseData = $response->getData(true);

            if ($responseData['success'] && isset($responseData['data']['url'])) {
                return [
                    'success' => true,
                    'url' => $responseData['data']['url'],
                    'message' => 'Payment initiated successfully'
                ];
            }

            throw new Exception($responseData['message'] ?? 'Payment initiation failed');
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function callBack(Request $request)
    {
        try {
            $paymentStatus = $request->get('status');
            Storage::put('Moyasar/response_' . time() . '.json', json_encode($request->all()));

            return strtolower($paymentStatus) === "paid";
        } catch (Exception $e) {
            logger()->error('Moyasar callback error: ' . $e->getMessage());
            return false;
        }
    }
}

<?php

namespace Modules\PaymentModule\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\PaymentModule\Interfaces\PaymentGatewayInterface;

class MoyasarPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    /**
     * Create a new class instance.
     */
    protected  $api_secrat;

    public function __construct()
    {
        $this->base_url = env('MOYASAR_BASE_URL');
        $this->api_secrat = env('MOYASAR_SECRET_KEY');
        $this->header = [
            "accept" => "application/json",
            "Content-Type" => "application/json",
            "Authorization" => "Basic ". base64_encode($this->api_secrat . ':')

        ];
    }

    public function sendPayment(Request $request){
        $data = $request->all();
        $userId = auth()->id();
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'User not authenticated'
            ];
        }
        if(!empty($data['success_url'])){
            $data['success_url'] = $request->getSchemeAndHttpHost() . '/api/v1/payment/callback?' .
            http_build_query([
                'booking_id' => $data['booking_id'],
                'user_id' => $userId,
                "redirect_url" => $data['redirect_url'],
                'status' => 'success'
            ]);
        }
        if(!empty($data['failure_url'])){
            $data['failure_url'] = $request->getSchemeAndHttpHost() . '/api/v1/payment/callback?' .
            http_build_query([
                'booking_id' => $data['booking_id'],
                'user_id' => $userId,
                "redirect_url" => $data['redirect_url'],
                'status' => 'failure'
            ]);
        }

        $response = $this->buildRequest('POST','/v1/invoices',$data);
        if($response->getData(true)['success']){
            return[
                'success' => true,
                'url' =>$response->getData(true)['data']['url']
            ];
        }
        return[
            'success' => false,
            'url' =>$response
        ];
    }

    public function callBack(Request $request)
    {
        $response_status = $request->get('status');
        Storage::put('Moyasar/response_'.time().'.json', json_encode($request->all()));
        if(isset($response_status) && strtolower($response_status) == "paid") {
            return true;
        }
        return false;
    }
}

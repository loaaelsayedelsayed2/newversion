<?php

namespace Modules\PaymentModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PaymentModule\Interfaces\PaymentGatewayInterface;

class MoyasarPaymentController extends Controller
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function paymentProcess(Request $request)
    {
        return $this->paymentGateway->sendPayment($request);
    }

    public function callback(Request $request)
    {
        $response = $this->paymentGateway->callBack($request);
        if ($response) {
            $bookingController = app()->make(\Modules\BookingModule\Http\Controllers\Api\V1\Customer\BookingController::class);

            $updateRequest = new \Illuminate\Http\Request();
            $updateRequest->merge([
                'payment_status' => 1,
                'booking_id' => $request->input('booking_id'),
            ]);

            $bookingController->bookingUpdate($updateRequest);
            return redirect()->route('payment.success');
        }
        return redirect()->route('payment.failed');
    }


    public function success()
    {
        return view('payment.payment-success');
    }
    public function failed()
    {
        return view('payment.payment-failed');
    }
}

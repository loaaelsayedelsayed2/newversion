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
        $request->validate([
            'amount' => 'required|numeric',
            'booking_id' => 'required',
            'redirect_url' => 'required|url'
        ]);

        $response = $this->paymentGateway->sendPayment($request);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'payment_url' => $response['url']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Payment initiation failed'
        ], 400);
    }

    public function callback(Request $request)
    {
        $paymentSuccess = $this->paymentGateway->callBack($request);
        $redirectUrl = $request->input('redirect_url') ?? $request->query('redirect_url');

        if ($paymentSuccess) {
            $bookingController = app()->make(\Modules\BookingModule\Http\Controllers\Api\V1\Customer\BookingController::class);

            $updateRequest = new Request([
                'payment_status' => 1,
                'booking_id' => $request->input('booking_id') ?? $request->query('booking_id'),
                'user_id' => $request->input('user_id') ?? $request->query('user_id')
            ]);

            $bookingController->bookingUpdate($updateRequest);

            return view('payment.payment-success', [
                'redirect_url' => $redirectUrl,
                'delay' => 5
            ]);
        }

        return view('payment.payment-failed', [
            'redirect_url' => $redirectUrl,
            'delay' => 5
        ]);
    }
}

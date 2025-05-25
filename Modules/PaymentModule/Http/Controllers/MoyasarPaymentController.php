<?php

namespace Modules\PaymentModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BookingModule\Entities\Booking;
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
        $validated = $request->validate([
            'booking_id' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'redirect_url' => 'nullable|url'
        ]);

        $response = $this->paymentGateway->sendPayment($request);

        if ($response['success']) {
            $booking = Booking::find($validated['booking_id']);
            $booking->is_paid = true;
            $booking->paid_by = 'customer';
            $booking->save();
            return response()->json([
                'success' => true,
                'payment_url' => $response['url'],
                'message' => $response['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Payment initiation failed'
        ], 400);
    }

    public function callback(Request $request)
    {
        $isPaymentSuccessful = $this->paymentGateway->callBack($request);
        $redirectUrl = $request->query('redirect_url', route('home'));
        $bookingId = $request->query('booking_id');
        $userId = $request->query('user_id');

        if ($isPaymentSuccessful) {
            $this->updateBookingPaymentStatus($bookingId, $userId);
            if ($request->query('redirect_url') == null) {
                return view('payment.payment-success');
            }
            return view('payment.payment-success', [
                'redirect_url' => $redirectUrl,
                'delay' => 5
            ]);
        }
        if ($request->query('redirect_url') == null) {
            return view('payment.payment-failed');
        }
        return view('payment.payment-failed', [
            'redirect_url' => $redirectUrl,
            'delay' => 5
        ]);
    }

    protected function updateBookingPaymentStatus($bookingId, $userId)
    {
        try {
            $bookingController = app()->make(\Modules\BookingModule\Http\Controllers\Api\V1\Customer\BookingController::class);

            $updateRequest = new \Illuminate\Http\Request();
            $updateRequest->merge([
                'payment_status' => 1,
                'booking_id' => $bookingId,
                'user_id' => $userId,
            ]);

            return $bookingController->bookingUpdate($updateRequest);

        } catch (\Exception $e) {
            logger()->error('Booking update failed: ' . $e->getMessage());
            return false;
        }
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

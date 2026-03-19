<?php

namespace App\Http\Controllers\razor;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorPaymentController extends Controller
{
    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email',
                'contact' => 'required',
                'name' => 'required'
            ]);

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $order = $api->order->create([
                'receipt' => 'order_' . uniqid(),
                'amount' => $request->amount * 100,
                'currency' => 'INR'
            ]);

            Payment::create([
                'order_id' => $order['id'],
                'amount' => $request->amount,
                'status' => 'created',
                'email' => $request->email,
                'contact' => $request->contact,
                'transaction_id' => null,
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json([
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'key' => env('RAZORPAY_KEY')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $attributes = [
            'razorpay_order_id' => $request->order_id,
            'razorpay_payment_id' => $request->payment_id,
            'razorpay_signature' => $request->signature
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);

            $payment = Payment::where('order_id', $request->order_id)->first();

            if (!$payment) {
                return response()->json(['status' => false, 'message' => 'Order not found'], 404);
            }

            if ($payment->status === 'success') {
                return response()->json([
                    'status' => true,
                    'message' => 'Already verified'
                ]);
            }

            // Capture payment
            $api->payment->fetch($request->payment_id)->capture([
                'amount' => $payment->amount * 100
            ]);

            $payment->update([
                'payment_id' => $request->payment_id,
                'signature' => $request->signature,
                'user_id' => $request->user_id,
                'status' => 'success'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully'
            ], 200);
        } catch (Exception $e) {

            Payment::where('order_id', $request->order_id)
                ->update(['status' => 'failed']);

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }
    }
}

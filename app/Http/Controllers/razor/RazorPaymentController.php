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
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $order = $api->order->create([
                'receipt' => 'order_rcptid_11',
                'amount' => $request->amount * 100, // paise me
                'currency' => 'INR'
            ]);

            // DB me save (status: created)
            Payment::create([
                'order_id' => $order['id'],
                'amount' => $request->amount,
                'status' => 'created',
                'email' => $request->email,
                'contact' => $request->contact,
                'transaction_id' => $request->transaction_id,
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json([
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'key' => env('RAZORPAY_KEY')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
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

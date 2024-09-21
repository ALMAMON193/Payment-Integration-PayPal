<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Facades\PayPal;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    /**
     * create transaction.
     *
     * return \Illuminate\Http\Response
     */
    public function createTransaction()
    {
        return view('welcome');
    }
    /**
     * process transaction.
     *
     * return \Illuminate\Http\Response
     */
    public function processTransaction(Request $request)
    {


        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        // Prepare the order data
        $orderData = [
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('successTransaction'),
                "cancel_url" => route('cancelTransaction'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->amount,
                    ]
                ]
            ]
        ];

        // Debugging the request
        Log::info('Creating PayPal order with data: ', $orderData);
        // Create order
        $response = $provider->createOrder($orderData);
        // dd($response);

        if (isset($response['id']) && $response['id'] != null) {
            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            return redirect()
                ->route('createTransaction')
                ->with('error', 'Something went wrong.');
        } else {
            return redirect()
                ->route('createTransaction')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    /**
     * success transaction.
     *
     * return \Illuminate\Http\Response
     */
    public function successTransaction(Request $request)
    {
        return "success";

    }
    /**
     * cancel transaction.
     *
     * return \Illuminate\Http\Response
     */
    public function cancelTransaction(Request $request)
    {
        return "cancel";
    }
}

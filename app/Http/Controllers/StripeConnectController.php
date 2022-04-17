<?php

namespace App\Http\Controllers;

use App\Models\StripeStateToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class StripeConnectController extends Controller
{
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function connect()
    {
        try {
            $seller = auth()->user();

            // Complete the onboarding process
            if (!$seller->is_stripe_connected) {
                $stripeToken = StripeStateToken::where('seller_id', $seller->id)->first();
                if (is_null($stripeToken)) {
                    $stripeToken = StripeStateToken::create([
                        'seller_id' => $seller->id,
                        'token'     => Str::random(),
                    ]);
                }

                // Check user has stripe connect id or not
                if (is_null($seller->stripe_connect_id)) {
                    // Create stripe account
                    $account = $this->stripeClient->accounts->create([
                        'country' => 'US',
                        'type'    => 'express',
                        'capabilities' => [
                            'transfers' => ['requested' => true],
                        ],
                        'email'   => $seller->email,
                    ]);

                    $seller->update([
                        'stripe_connect_id' => $account->id,
                    ]);
                    $seller->fresh();
                }

                $onboardLink = $this->stripeClient->accountLinks->create([
                    'account'     => $seller->stripe_connect_id,
                    'refresh_url' => route('stripe.connect'),
                    'return_url'  => route('stripe.save.account', $stripeToken->token),
                    'type'        => 'account_onboarding',
                ]);

                return redirect($onboardLink->url);
            }
            $loginLink = $this->stripeClient->accounts->createLoginLink($seller->stripe_connect_id);
            return redirect($loginLink->url);
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }

    public function saveStripeAccount($token)
    {
        try {
            $stripeToken = StripeStateToken::where('token', $token)->first();
            if (is_null($stripeToken)) {
                abort(404);
            }

            $seller = User::find($stripeToken->seller_id);
            $seller->update([
                'is_stripe_connected' => true
            ]);
            session()->flash('success', 'Stripe connect account connected successfully.');
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }

    public function storePayment(Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'stripeToken' => 'required',
                'amount'      => 'required|numeric|min:1',
            ]);

            $seller = auth()->user();

            if (is_null($seller)) {
                abort(404);
            }

            if (!$seller->is_stripe_connected) {
                // throw error message
                return back()->with('error', 'Please connect your stripe connect account first!');
            }


            $amount = $request->amount * 100;

            // Purchase Item
            $charge = $this->stripeClient->charges->create([
                'amount'      => $amount,
                'currency'    => 'usd',
                'source'      => $request->stripeToken,
                'description' => 'Test Charge for stripe connect',
            ]);

            $admin_percentage = ($amount * 15) / 100;
            $transfer_amount = $amount - $admin_percentage;

            // Transfer to seller account
            $this->stripeClient->transfers->create([
                'amount'             => $transfer_amount,
                'currency'           => 'usd',
                'source_transaction' => $charge->id,
                'destination'        => $seller->stripe_connect_id,
                'description'        => 'Test Transfer to seller connect account',
            ]);

            return redirect()->route('home')->with('success', 'Payment Successful & Amount transferred to seller account!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }

    public function makePaymentPage()
    {
        return view('payment');
    }
}

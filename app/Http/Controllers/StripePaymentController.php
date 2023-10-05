<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Session;
use App\Models\Subscription;
use App\Models\TrianerAmount;
use Illuminate\Support\Facades\Validator;
use Stripe;
use Auth;
use Stripe\Transfer;

class StripePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->stripe  = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function stripe()
    {
        return view('stripe');
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required',
            'cardNumber' => 'required',
            'month' => 'required',
            'year' => 'required',
            'cvv' => 'required',
            'amount'=>'required'
        ]);

        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }

        $token = $this->createToken($request);
        if (!empty($token['error'])) {

            $message = [
                'message' => $token['error']
            ];
            return response()->json($message,500);

        }
        if (empty($token['id'])) {

            $message = [
                'message' => "Payment failed"
            ];
            return response()->json($message,500);
        }

        $charge = $this->createCharge($token['id'], $request->amount);
        if (!empty($charge) && $charge['status'] == 'succeeded') {

            $message = [
                'message' => "Payment completed."
            ];
            $user =Auth::user();
            $user->paystatus="complete";
            $user->save();
            return response()->json($message,200);
        } else {

            $message = [
                'message' => "Payment failed."
            ];
            return response()->json($message,500);
        }
        return 0;
    }

    private function createToken($cardData)
    {
        $token = null;
        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'number' => $cardData['cardNumber'],
                    'exp_month' => $cardData['month'],
                    'exp_year' => $cardData['year'],
                    'cvc' => $cardData['cvv']
                ]
            ]);
        } catch (CardException $e) {
            $token['error'] = $e->getError()->message;
        } catch (Exception $e) {
            $token['error'] = $e->getMessage();
        }
        return $token;
    }

    private function createCharge($tokenId, $amount)
    {
        $charge = null;
        try {
            $charge = $this->stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $tokenId,
                'description' => 'My first payment'
            ]);
        } catch (Exception $e) {
            $charge['error'] = $e->getMessage();
        }
        return $charge;
    }

    public function subscribe(request $request)
    {

        // return $request->all();

        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'cvc' => 'required',
            'exp_month'=>'required',
            'exp_year'=>'required',
            'plan_id'=>'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        // $card_details=[];
        // $transaction_details=[];
        $user=Auth::user();





            try{

                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));



                $token=$stripe->tokens->create([

                    'card' => [
                        'number' => $request->card_number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'cvc' => $request->cvc,

                    ],
                ]);


                $customer = \Stripe\Customer::create(array(
                    'email'    => $user->email,
                    'description' => "Bless plan",
                    'source'=>$token->id,
                ));

                $stripe->charges->create([
                    'amount' => 200,
                    'currency' => 'usd',
                    'customer'=>$customer->id,
                    'description' => 'Subscription Plan',

                ]);

                $sub= $stripe->subscriptions->create([
                    'customer' => $customer->id,
                    'items' => [
                        ['plan' => $request->plan_id],
                    ],
                ]);



                if( Subscription::where('user_id',$user->id)->exists()){
                    $subId =Subscription::where('user_id',$user->id)->first();
                    $new_subscription = Subscription::whereId($subId->id)->update([
                        'user_id' => $user->id,
                        'subscription_id' => $sub->id,
                        'customer' => $customer->id,
                        'plan_id' => $sub->plan->id,
                        'subscription_start' => date("Y-m-d H:i:s", $sub->current_period_start),
                        'subscription_end' => date("Y-m-d H:i:s", $sub->current_period_end),
                        'plan_amount' => $sub->plan->amount,
                        'currency' => $sub->plan->currency,
                        'interval' => $sub->plan->interval,
                        'payment_status' => 'Success',
                        'comments' => 'Ok',
                    ]);
                }
                else {
                    $new_subscription = Subscription::create([
                        'user_id' => $user->id,
                        'subscription_id' => $sub->id,
                        'customer' => $customer->id,
                        'plan_id' => $sub->plan->id,
                        'subscription_start' => date("Y-m-d H:i:s", $sub->current_period_start),
                        'subscription_end' => date("Y-m-d H:i:s", $sub->current_period_end),
                        'plan_amount' => $sub->plan->amount,
                        'currency' => $sub->plan->currency,
                        'interval' => $sub->plan->interval,
                        'payment_status' => 'Success',
                        'comments' => 'Ok',
                    ]);

                }

//                if($recipient=User::where('referal_code',$user->refered_code)->first())
//                {
//                    $wallet=Wallet::where('user_id',$recipient->id)->first();
//
//                    $wallet->update(['amount'=>$wallet->amount+1]);
//
//                    \Mail::to($recipient->email)->send(new \App\Mail\Subscriber($recipient,$user));
//                }

                $user->paystatus='complete';
                $user->subscription_status =1;
                $user->save();
                $traineraccountid =User::find($request->trainerid);
                $account_id =$traineraccountid->account_id;
                $ammount =$sub->plan->amount;
                $client_id= $this->processPayment($account_id ,$ammount);
                TrianerAmount::create(['clientSecret'=>$client_id,'plan_id'=>$request->plan_id,'amount'=>$sub->plan->amount,'trainer_id'=>$request->trainerid]);

//                \Mail::to($user->email)->send(new \App\Mail\SubscriptionCreated($user));


            }
            catch(\Stripe\Exception\CardException $e) {
                // Since it's a decline, \Stripe\Exception\CardException will be caught
                $e = $e->getMessage();
                return response()->json([
                    'status'=>false,
                    "message" => $e
                ]);
            }
            catch (\Stripe\Exception\RateLimitException $e) {
                $e = $e->getMessage();

                return response()->json([
                    'status'=>false,
                    "message" => $e
                ]);
                // Too many requests made to the API too quickly
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Invalid parameters were supplied to Stripe's API
                $e = $e->getMessage();
                return response()->json([
                    'status'=>false,
                    "message" => $e
                ]);
            } catch (\Stripe\Exception\AuthenticationException $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                $e = $e->getMessage();
                return response()->json([
                    'status'=>false,
                    "message" => $e
                ]);
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                // Network communication with Stripe failed
                $e = $e->getMessage();
                return response()->json([
                    'status'=>false,
                    "message" => $e
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                $e = $e->getMessage();
                return response()->json([
                    'status'=>false,
                    "message" => $e
                ]);
            }
            return response()->json([
                'status'=>True,
                "message" => 'Subscribed Successfully',
                "subscription"=>$new_subscription,
            ]);



    }
    public function cancel_subscription(){
        User::whereId(Auth::user()->id)->update(['subscription_status'=>0]);
        return response()->json([
            "status"=>true,
            'message'=>"Subscription Cancelled"
        ]);
    }
    public function processPayment($account_id ,$ammount)
    {
        $paymentIntent =  Transfer::create([
            'amount' => $ammount * 100, // Amount in cents
            'currency' => 'inr',

                'destination' => $account_id, // Replace with the Stripe Connect account ID

        ]);

        // Retrieve the payment intent client secret
        $clientSecret = $paymentIntent->client_secret;
        return $clientSecret;
    }
}

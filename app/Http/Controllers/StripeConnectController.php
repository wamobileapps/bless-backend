<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;
use Stripe\OAuth;
use Stripe\Stripe;
use Stripe\StripeClient;
use App\Models\User;
class StripeConnectController extends Controller
{

    private $stripe;
    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.api_keys.secret_key'));
        Stripe::setApiKey(config('stripe.api_keys.secret_key'));
    }

    public function index()
    {
        $userId = Auth::user()->id;
        // print_r($userId);die('gfdgsdfgfds');
        $queryData = [
            'response_type' => 'code',
            'client_id' => config('stripe.client_id'),
            'scope' => 'read_write',
            'redirect_uri' => config('stripe.redirect_uri'),
        ];
        
        return config('stripe.authorization_uri').'?'.http_build_query($queryData);

    }

    public function redirect(Request $request )
    // public function redirect(Request $request)
    {

        $userId = $request->query('user_id');

        $token = $this->getToken($request->code);
        if(!empty($token['error'])) {

            return $token['error'];
        }
        $connectedAccountId = $token->stripe_user_id;
        $account = $this->getAccount($connectedAccountId);
         if(!empty($account['error'])) {

             return $account['error'];
         }


        return $account['id'];
    }

    // public function save_accountid(Request $request)
    // {
    //     return $user;
    // }

    private function getToken($code)
    {
        $token = null;
        try {
            $token = OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code
            ]);
        } catch (Exception $e) {
            $token['error'] = $e->getMessage();
        }
        return $token;
    }


    private function getAccount($connectedAccountId)
    {
        $account = null;
        try {
            $account = $this->stripe->accounts->retrieve(
                $connectedAccountId,
                []
            );
        } catch (Exception $e) {
            $account['error'] = $e->getMessage();
        }
        return $account;
    }
}

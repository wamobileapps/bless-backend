<?php

namespace App\Http\Controllers;

use JWTAuth;
use Auth;
use Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Message;
use App\Models\GroupSchedule;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;
use DB;
use App\Models\BankDetails;
use Carbon\Carbon;
use api;
use Twilio\Rest\Client;
class ApiController extends Controller
{
    public function register(Request $request)
    {

        //Validate data
        //return $request->all();
        $data = $request->only('first_name', 'last_name','dob','country','state','city','email', 'password','password_confirmation');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'mobile_number'=>'required|min:10',
           
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
        $originalDate = $request->dob;
        $newDate = date("Y-m-d", strtotime($originalDate));
        //Request is valid, create new user
    
        $user = User::create([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'device_type'=>$request->device_type,
            'provider_id'=>$request->provider_id,
            'provider_name'=>$request->provider_name,
            'device_type'=>$request->device_type,
            'device_token'=>$request->device_token,
            'fcm_token'=>$request->fcm_token,
            
        ]);
          return $this->authenticate($request);
        //User created, return success response
    //    return response()->json([
    //        'success' => true,
    //        'message' => 'User created successfully',
    //        'data' => $user
    //    ], Response::HTTP_OK);
    }
    public function edit_profle($id){

        $user=User::find($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }


    public function update_profile(Request $request){
            $id =Auth::user()->id;
            $data=$request->all();
        // if ($request->hasFile('photo')) {
        //     $imageName = $request->photo->getClientOriginalName();
        //     $imgrand=Str::random(15);
        //     $request->photo->move('/home/bookedyo/public_html/webroot/img/profile_img/', $imgrand.$imageName);
        //     $data['photo']=$imgrand.$imageName;
        // }
      User::whereId($id)->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Update Successfully'
        ], Response::HTTP_OK);

    }
    public function AddBankDetails(Request $request)
    {
    //   return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'account_number'=>'required|numeric',
            'ifsc_code'=>'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
            
        }
            BankDetails::create([
              'user_id'=>Auth::user()->id,
              'name'=>$request->name,
              'account_number'=>$request->account_number,
              'ifsc_code'=>$request->ifsc_code,


            ]);

            return response()->json(['status'=>True,
            "message" => 'Added Successfully ']);
        

    }
    public function GetBankDetails()
    {
      $bankDetails =Auth::user()->bankDetails;

        return response()->json([
            'success' => true,
            'Bank Details'=>$bankDetails,
        ], Response::HTTP_OK);
    }
    
public function UpdateBankDetails(Request $request)
{
   
    // return Auth::user()->id;
   
   
    BankDetails::where('user_id',Auth::user()->id)->update([
       
    'name'=>$request->name,
    'account_number'=>$request->account_number,
    'ifsc_code'=>$request->ifsc_code,

]);
            return response()->json([
                'status'=>True,
                "message" => 'Updated Successfully '
            ]);



}


        public function deactivate_account(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed'],
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
        if (Hash::check($request->password, Auth::user()->password)) {
            User::whereId(Auth::user()->id)->update(['is_active'=>1]);
            return response()->json([
                'status'=>True,
                "message" => 'Deactivate Successfully '
            ]);
        }
        else{
            return response()->json([
                'status'=>False,
                "message" => 'Password Mismatch'
            ]);
        }
    }
     public function ResetPassword(Request $request)
     {
     
       
        $validator = Validator::make($request->all(), [
            'password' => ['required'],
            'email' => ['required'],
            
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
       
             $user=User::where('email',$request->email)->first();
             
             if (isset($user)) {
             $user-> update(['password'=>bcrypt($request->password)]);
            
             return response()->json([
                'status'=>True,
                "message" => 'Updated Successfully '
            ]);
        }
        else{
            return response()->json([
                'status'=>True,
                "message" => 'Invalid Email '
            ]);

        }


     }

    public function change_password(Request $request){
        $validator = Validator::make($request->all(), [
            'currentpassword' => ['required'],
            
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
        if (Hash::check($request->currentpassword, Auth::user()->password)) {
             User::whereId(Auth::user()->id)->update(['password'=>bcrypt($request->password)]);
            return response()->json([
                'status'=>True,
                "message" => 'Updated Successfully '
            ]);
        }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Current Password Does Not Match',
                ], 500);
            }

    }
    
    public function authenticate(Request $request)
    {

        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',

                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Could not create token.',
            // ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
            'user_details'=>Auth::user()
        ]);
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
         public function forgot(Request $request)
         {
             $date = Carbon::now();
             $date=strtotime($date);
             $futureDate = $date+(60*5);
            $expiry=date("Y-m-d H:i:s", $futureDate);
            
             $credentials = request()->validate(['email' => 'required|email']);
             $user = User::where('email', $request->email)->first();
             
            //  if (isset($user)) {
            //  $verification_code = Str::random(8);
             
            //  $user->verification_code=$verification_code;
            //  $user->code_expiry=$expiry;
            //  $user->save();
            // //  $account_sid = getenv("TWILIO_SID");
            // //  $auth_token = getenv("TWILIO_AUTH_TOKEN");
            // //  $twilio_number = getenv("TWILIO_NUMBER");
            // //  $client = new Client($account_sid, $auth_token);
            // //   $client->messages->create("+917986263826", 
            // // ['from' => $twilio_number, 'body' => "otp for friendly money verification :".$verification_code] );

            //  \Mail::to($user->email)->send(new \App\Mail\VerificationCode($user));
            
            //  return response()->json([
            //     'status'=>true,
            //     "message" => 'Please check mail for verification code'
            // ]);
             
        // }
             if (isset($user)) {

                 Password::sendResetLink($credentials);

                 return response()->json([
                     'status'=>true,
                     "message" => 'Reset password link sent on your email address.'
                 ]);
             }
             else{
                 return response()->json([
                     'status'=>false,
                     "message" => 'Email Id is not Exist '
                 ]);
             }
         }


         public function verifyCode(Request $request)
         {
             
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'code' => 'required'
            ]);
    
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 200);
            }
             
             $user=User::where('email',$request->email)->first();
            
             $credentials = request()->validate(['email' => 'required|email']);
             $date = Carbon::now();
             $date=strtotime($date);
             $now=date("Y-m-d H:i:s", $date);
            
             if($user->code_expiry<$now)
             {
                return response()->json([
                    'status'=>true,
                    "message" => 'Verification Code Expired'
                ]);

             }

else{
           if($request->code==$user->verification_code)
           {
            // Password::sendResetLink($credentials);


            
                 return response()->json([
                      'status'=>true,
                      "message" => 'Success.'
                  ]);
           }
else{
    return response()->json([
                'status'=>false,
                "message" => 'Please enter correct verification code'

    ]);
}

         }
        }

        public function VerifyReferedCode(Request $request)
        {
            $request->all();
            $exists=User::where('referal_code',$request->referal_code)->first();
            if($exists){
                $user=Auth::user();
              $user->refered_code=$request->referal_code;
              $user->save();

                return response()->json([
                    'status'=>true,
                    "message" => 'success'
    
        ]);
    }
        else{
            return response()->json([
                'status'=>false,
                "message" => "Referal code doesn't exists"

    ]);

        }

        }

        public function InvitedContacts()
        {
            return User::where('refered_code',Auth::user()->referal_code)->where('id','!=',Auth::user()->id)->get();
        }
    public function get_user(Request $request)
    {


        $user = JWTAuth::authenticate($request->bearerToken());

        return response()->json(['user' => $user]);
    }
    public function deleteAccount(Request $request)
    {

            $uid = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed'],
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
        if (Hash::check($request->password, Auth::user()->password)) {


            Message::where('sender_users_id',$uid)->delete();
            DB::table('invitations')->where('receiver_users_id',$uid)->delete();
            DB::table('invitations')->where('sender_users_id',$uid)->delete();
            DB::table('schedules_users')->where('users_id', $uid)->delete();
            DB::table('schedules_user_notifications')->where('receiver_users_id', $uid)->delete();
            DB::table('schedules')->where('users_id', $uid)->delete();
            DB::table('schedules_user_notifications')->where('sender_users_id', $uid)->delete();
            DB::table('all_notifications')->where('sender_users_id', $uid)->delete();
            $user = User::find(Auth::user()->id);

            Auth::logout();

            if ($user->delete()) {

                JWTAuth::invalidate($request->token);
                return response()->json([
                    'status'=>True,
                    "message" => 'Deleted Successfully '
                ]);
            }

            }
        return response()->json([
            'status'=>True,
            "message" => 'Password Mismatch! '
        ]);
        }

        public function socialloginwith()
        {
           $credentials = request(['provider_id', 'password']);
           $email=request('email');
           $devices = request(['device_type', 'device_token', 'fcm_token']);
           if (!$token = JWTAuth::attempt($credentials)) {
    
                    return response()->json(['error' => 'Unauthorized'], 401);
             }
        
           User::where('email',$email)
              ->update(['provider_id' => $credentials['provider_id'],'device_type' => $devices['device_type'],'device_token' => $devices['device_token'],'fcm_token' => $devices['fcm_token']]);
              return response()->json([
                'token'=>$token,
                "status" => 200,
            ]);
        }

        public function socialLogin(Request $request){
   
            //return $request;
            //dd($request->all());
            if($user=User::where('provider_id', '=', $request->provider_id)->first())
            {
            $email=$user->email;
            $name=$user->name;
            $provider_name=$user->provider_name;
            $provider_id=$request->provider_id;
            $device_type=$user->device_type;
            $device_token=$user->device_token;
            $fcm_token=$user->fcm_token;
            $mobile_number=$user->mobile_number;
    
        }
    else{
    
          $email=$request->email;
          $name=$request->name;
          $provider_name=$request->provider_name;
          $provider_id=$request->provider_id;
          $device_type=$request->device_type;
          $mobile_number=$request->mobile_number;
          $device_token=$request->device_token;
          $fcm_token=$request->fcm_token;
        }
    
        if (User::where('email', '=', $email)->count() > 0) {
    
            $ex = User::where('email',$email)->first();
    
            if ($ex->provider_name == Null) {
    
                $email = $email;
                $password = Hash::make($request->password);
    
               return $this->socialloginwith($device_type,$device_token,$fcm_token);
            }
    
    
          elseif (User::where('provider_id', '=', $provider_id)->count() > 0) {
            
                // $email = $request->email;
                $password = Hash::make($request->password);
                return $this->socialloginwith($device_type,$device_token,$fcm_token);
          }
    
         else{
                     User::where('email', $email)
            ->update(['provider_id' => $provider_id,'provider_name' => $provider_name]);
    
                $email = $email;
                $password = Hash::make($request->password);
    
               return $this->socialloginwith($device_type,$device_token,$fcm_token);
            }
              
            }
       
    else{
    
       if (User::where('provider_id', '=', $provider_id)->count() > 0) {
          $email = $email;
                $password = Hash::make($request->password);
                return $this->socialloginwith($device_type,$device_token,$fcm_token);
        }
        else{
          $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'mobile_number' => $request->mobile_number,
          'provider_id' => $request->provider_id,
          'provider_name' => $request->provider_name,
          'password' => Hash::make($request->password),
          'device_type' => $request->device_type,
          'device_token' => $request->device_token,
           'fcm_token'=>$request->fcm_token,
          ]);
          $email = $email;
          $password = $request->password;
         
          return $this->authenticate($request);
        }
                
            }
       
       }
    public function getipaddress(){
        $ipaddress = getenv('REMOTE_ADDR');
        echo "Your IP Address is " . $ipaddress;
        die('heet');
    }
}

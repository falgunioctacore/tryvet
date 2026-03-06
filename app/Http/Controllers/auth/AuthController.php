<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends ResponseController
{
    public function register(Request $request){
            $validator=Validator::make($request->all(),[
                'name'=>'required|string',
                'email'=>'required|string|email|unique:users',
                'password'=>'required|string|min:5|confirmed',
                
            ]);
            if($validator->fails()){
                return response()->json([
                    'status'=>0,
                    'errors'=>$validator->errors()
                ],422);
            }

              $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>$request->password,
                'device_id'=>$request->device_id,
            ]);

            return response()->json([
                'status'=>1,
                'user'=>$user,
                'bearer_token'=>$user->createToken("API TOKEN")->plainTextToken,
            ],201);
    }

    // public function login(Request $request){
    //       $validatorRules = [
    //           'email' => 'required|email',
    //           'password' => 'required|string|min:5',
    //           'admin' => 'required|min:0|max:1'
    //           ];
    //         // Conditionally add 'device_id' validation if admin is 0
    //       if ($request->admin == 0) {
    //               $validatorRules['device_id'] = 'required';
    //                 // $validatorRules['device_id'] = 'nullable';
                
    //       }
    //       $validator = Validator::make($request->all(), $validatorRules);

    //       $query = User::where(['email' => $request->email, 'admin' => $request->admin]);

    //   // If admin is 0, include device_id in the query
    //   if ($request->admin == 0 && isset($request->device_id)) {
    //           $query->where('device_id', $request->device_id);
    //   }

    //       $user = $query->first();
    
    //         if(empty($user)){
    //             return response()->json([
    //                 'status'=>0,
    //                 'message'=>'unauthorized'
    //             ],401);
    //         }
            
        
    //         if($validator->fails()){
    //             return response()->json([
    //                 'status'=>0,
    //                 'error'=>$validator->errors()
    //             ],422);
    //         }
              
    //         if(Auth::attempt($request->only('email','password'))){
    //             $user=Auth::user();
    //             if($user->admin==0){
    //              $admin=0;
    //             }
    //             else{
    //              $admin=1; 
    //             }
    //             return response()->json([
    //               'status'=>1,
    //               'admin'=>$admin,
    //               'bearer_token'=>$user->createToken('API TOKEN')->plainTextToken,
    //               'user'=>$user
    //             ]);
    //         }
    //         $columns=Schema::getColumnListing('users');
    //         $user=new User();
    //         // return response()->json([
    //         //     'status'=>0,
    //         //     'error'=>'unauthorized',
    //         //     // 'data'=>$user->getFillable(),
    //         //  ],401);
            
    //           return $this->failedResponseWithData('unauthorize',[
    //                  "id"=>'',
    //                  "name"=> "",
    //                  "email"=>"",
    //                  "email_verified_at"=> "",
    //                  "current_team_id"=>"",
    //                  "profile_photo_path"=>"",
    //                  "created_at"=>"",
    //                  "updated_at"=> "",
    //                   "two_factor_confirmed_at"=>"",
    //                   "mobile_no"=> "",
    //                   "state"=> "",
    //                   "city"=> "",
    //                   "occupation"=>"",
    //                   "deleted_at"=> "",
    //                   "admin"=> "",
    //                   "buisness"=>"",
    //                   "is_trial"=>"",
    //                   "location"=>"",
    //                   "device_id"=>"",
    //                   "profile_photo_url"=> ""
    //                 ],401);
    // }
    
    public function login(Request $request){
    // Step 1: Validation rules
    $rules = [
        'email'    => 'required|email',
        'password' => 'required|string|min:5',
        'admin'    => 'required|integer|min:0|max:1',
    ];

    // Conditionally add 'device_id' validation if admin = 0
    if ((int)$request->admin === 0) {
        // $rules['device_id'] = 'required';
    }

    $validator = Validator::make($request->all(), $rules);

    // Step 2: Validation fail
    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'error'  => $validator->errors(),
        ], 422);
    }

    // Step 3: Build query
    $query = User::where('email', $request->email)
                 ->where('admin', $request->admin);

    if ((int)$request->admin === 0 && $request->has('device_id')) {
        // $query->where('device_id', $request->device_id);
    }

    $user = $query->first();

    // Step 4: Check user existence
    if (!$user || !Hash::check($request->password, $user->password)) {
        // 👇 SAME failedResponseWithData as your original
        return $this->failedResponseWithData('unauthorize', [
            "id" => '',
            "name" => "",
            "email" => "",
            "email_verified_at" => "",
            "current_team_id" => "",
            "profile_photo_path" => "",
            "created_at" => "",
            "updated_at" => "",
            "two_factor_confirmed_at" => "",
            "mobile_no" => "",
            "state" => "",
            "city" => "",
            "occupation" => "",
            "deleted_at" => "",
            "admin" => "",
            "buisness" => "",
            "is_trial" => "",
            "location" => "",
            "device_id" => "",
            "profile_photo_url" => ""
        ], 401);
    }

    // Step 5: Successful login
    $token = $user->createToken('API TOKEN')->plainTextToken;

    return response()->json([
        'status'       => 1,
        'admin'        => $user->admin,
        'bearer_token' => $token,
        'user'         => $user
    ]);
 }


    public function logout(Request $request){
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status'=>1,
                'message'=>'logged out successfully']);
    }

    public function userDetail(Request $request){
         $user=$request->user();
      
         return response()->json([
            'status'=>1,
            'userdetails'=>$user],200);
    }

    // public function passwordUpdate(){
    //     Validator::make($input, [
    //         'current_password' => ['required', 'string', 'current_password:web'],
    //         'password' => $this->passwordRules(),
    //     ], [
    //         'current_password.current_password' => __('The provided password does not match your current password.'),
    //     ])->validateWithBag('updatePassword');

    //     $user->forceFill([
    //         'password' => Hash::make($input['password']),
    //     ])->save();
    // }
} 
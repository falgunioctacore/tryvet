<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Can;
use Carbon\Carbon;


class UserController extends ResponseController
{
    // public function __construct(){
    //     new Middleware('role:admin',['store','update','delete','index','show']);
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $request)
    {
        $validatore=$request->validated();
        // return $validatore;
        $id=null;
        if(isset($validatore['id'])){
            $id=$validatore['id'];
        }
        if(is_null($id)){
             $users = User::with('latestSubscription')->get();
        }
        else{
           $users = User::with('latestSubscription')->find($id);
        }
        if(empty($users)){
            return $this->failedResponse('user not found',404);
        }
        return response()->json([
            'status'=>1,
            'users'=>$users
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $user=User::create($request->validated());
        if(!empty($user)){
            return response()->json([
                'status'=>1,
                'user'=>$user
            ],201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user=User::find($id);
        if(!(empty($user))){
            return response()->json([
                'status'=>1,
                'user'=>$user],200);
        }
        else{
            return response()->json([
                'status'=>0,
                'message'=>'user is not found',
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest  $request)
    {
        $validator=$request->validated();
        $user=User::find($validator['id']);
        if(empty($user)){
            return response()->json([
                'status'=>0,
                'message'=>'user not found'],404);
       }

       $user->update($request->validated());
       return response()->json([
        'status'=>1,
        'message'=>'user is updated successfully'],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
       $validator=Validator::make($request->all(),[
        'id'=>'required|numeric',
       ]);
       if($validator->fails()){
        return response()->json([
            'status'=>0,
            'message'=>'validation is failed',
            'errors'=>$validator->errors()
        ]);
       }
        $user=User::find($request->id);
        if(!empty($user)){
            $user->delete();
            return response()->json([
                'status'=>1,
                'message'=>'user is deleted successfully'],200);
        }
        else{
            return response()->json([
                'stauts'=>0,
                'message'=>'user not found'],404);
        }
    }
    
    public function expiredOrExpiringUsers()
    {
    
      $users = User::with('latestSubscription')
        ->get()
        ->sortBy(function ($user) {
            $endDate = optional($user->latestSubscription)->end_date;
            return $endDate ? Carbon::parse($endDate) : Carbon::now()->addYears(100);
        })
        ->values(); // Reset index

    return response()->json([
        'status' => 1,
        'users' => $users
    ]);
        
    }

}

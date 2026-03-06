<?php

namespace App\Http\Controllers\notification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdNotificationRequest;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\StoreRequestZone;
use App\Http\Requests\UpdateNotificationReques;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdNotificationRequest $request)
    {
        $validator=$request->validated();
        $id=null;
        if(isset($validator['id'])){
             $id=$validator['id'];
        }
        if(is_null($id)){
            $notifications=Notification::all();
        }
        else{
          $notifications=Notification::find($id);
        }
        
        if(empty($notifications)){
            return $this->failedResponse('notificaiton not found',404);
        }
        return $this->successResponse('notification is fetched is successfully',$notifications,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request)
    {
        $notification=Notification::create($request->validated());
        return $this->messageResponse('notification is added success fully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notification=Notification::find($id);
        if(empty($notification)){
            return $this->failedResponse('not notification found',404);
        }
        return $this->successResponse('notification is fetched successfully',$notification,201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationReques $request)
    {
        $validator=$request->validated();
        $notification=Notification::find($validator['id']);
        if(empty($notification)){
            return $this->failedResponse('not notification found',404);
        }
        $notification->update($request->validated());
       return $this->messageResponse('notification is updated success fully',201);
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
              'errors'=>$validator->errors(),
          ],422);
        }
        $notification=Notification::find($request->id);
        if(empty($notification)){
            return $this->failedResponse('not notification found',404);
        }
        $notification->delete();
        return $this->messageResponse('notification is deleted successfully',200);
    }
}

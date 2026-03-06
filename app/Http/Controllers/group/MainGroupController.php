<?php

namespace App\Http\Controllers\group;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\StoreMainGroupRequest;
use App\Http\Requests\UpdateMainGroupRequest;
use App\Models\Group;
use App\Models\MainGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MainGroupController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups=MainGroup::orderByDesc('created_at')->get();
        return response()->json([
            'status'=>1,
            'groups'=>$groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMainGroupRequest $request)
    {
        $validated=$request->validated();
        MainGroup::create($validated);
        return response()->json([
            'status'=>1,
            'message'=>'Main Group Created Successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group=MainGroup::find($id);
        if($$group){
            return response()->json([
                'status'=>1,
                'group'=>$group
            ]);
        }

        return response()->json([
            'status'=>0,
            "message"=>'Not found'
        ],404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMainGroupRequest $request)
    {
        $validated=$request->validated();
        $group=MainGroup::find($validated['id']);
        if($group){
            $group->update(['name'=>$validated['name']]);
            return response()->json([
                'status'=>1,
                'message'=>'updated successfully'
            ]);
        }

        return response()->json([
            'status'=>0,
            'message'=>'Not Found',
        ],404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
       $validator=Validator::make($request->all(),[
            'id'=>'required'
        ]);
          if($validator->fails()){
            return response()->json([
                'status'=>0,
                'message'=>$validator->errors()
            ],422);
         }

        $group=MainGroup::find($request->id);

        if(empty($group)){
            return $this->failedResponse('not found ',404);
        }
            $group->delete();
            return response()->json([
                'status'=>1,
                'message'=>'Group Deleted Successfully'
            ]);
     }
    
}

<?php

namespace App\Http\Controllers\state;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\returnSelf;

class StateController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $request)
    {
        $validator=$request->validated();
        $id=null;
        $states="";
        if(isset($validator['id'])){
            $id=$validator['id'];
        }
        if(is_null($id)){
            $states=State::all();
        }
        else{
            $states=State::find($id);
        }
        if(empty($states)){
            return $this->failedResponse('failed to fetch',404);
        }
        return $this->successResponse('states  Fetched successfully',$states);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request)
    {
        $validated=$request->validated();
        $state=State::create($validated);
        return $this->messageResponse('state is added successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request)
    {
        $validator=$request->validated();
        $state=State::find($validator['id']);
        if(empty($state)){
            return $this->failedResponse('state not found',404);
        }   
        $state->update($request->validated());
        return $this->messageResponse("state is updated successfully",200);
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
        $state=State::find($request->id);
        if(empty($state)){
            return $this->failedResponse('state not found',404);
        }
        $state->delete();
        return $this->messageResponse("state is deleted successfully",200);
    }

    public function statewithDistricts(IdRequest $request){
       
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
        $states=State::with(['districts'])->find($request->id);
        // if(is_object($states)){
        //     $this->failedResponse('not found',404);
        // }
        return $this->successResponse('states  Fetched successfully',$states);
    }

    public function statesWithDistricts(Request $request){
        $states=State::with(['districts'])->get();
     
        return $this->successResponse('all states is fetched successfully',$states);
    }
}

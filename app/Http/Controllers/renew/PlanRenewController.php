<?php

namespace App\Http\Controllers\renew;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdPlanRenewRequest;
use App\Http\Requests\IdRequest;
use App\Http\Requests\StorePlanRenewRequest;
use App\Http\Requests\UpdatePlanRenewRequest;
use App\Models\PlanRenew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanRenewController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $request)
    {
        $validator=$request->validated();
        $id=null;
        $plans="";
        if(isset($validator['id'])){
         $id=$validator['id'];
        }
        if(is_null($id)){
            $plans=PlanRenew::all();
        }
        else{
            $plans=PlanRenew::find($id);
        }
        if(empty($plans)){
            return $this->failedResponse('failed to fetch',404);
        }
        return $this->successResponse('Plans Renew Fetched successfully',$plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanRenewRequest $request)
    {
        $plan=PlanRenew::create($request->validated());
        if(!empty($plan)){
           return $this->messageResponse('plan renew is added successfully',201);
        }
       
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan=PlanRenew::find($id);
        if(empty($plan)){
           return $this->failedResponse('Plan Renew is Not Found',404);
        }

      return $this->successResponse('Feteched Plan Renew Successfully',$plan,201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanRenewRequest $request)
    {
        $validator=$request->validated();
        
        $plan=PlanRenew::find($validator['id']);
        if(empty($plan)){
           return $this->failedResponse("Plan Renew is Not Found",404);
        }
        
        $plan->update($request->validated());

        return $this->messageResponse("plan renew is updated successfully",200);
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

        $plan=PlanRenew::find($request->id);
    
        if(empty($plan)){
              return $this->failedResponse("plan renew is not found",404);
        }
        $plan->delete();
        return $this->messageResponse('plan renew is deleted successfully',200);
    }
}

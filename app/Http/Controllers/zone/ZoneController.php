<?php

namespace App\Http\Controllers\zone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\StoreRequestZone;
use App\Http\Requests\UpdateZoneRequest;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $idRequest)
    {
        $validator=$idRequest->validated();
        $id=null;
        if(isset($validator['id'])){
            $id=$validator['id'];
        }
        if(is_null($id)){
            $zones=Zone::all();
        }
        else{
            $zones=Zone::find($id);
            if(empty($zones)){
                return $this->failedResponse('not found',404);
            }
        }
        return $this->successResponse("All Zone list",$zones,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequestZone $request)
    {
        $zone=Zone::create($request->validated());

        return $this->messageResponse('zone is created successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $zone=Zone::find($id);
        if(empty($zone)){
            return $this->failedResponse('zone not found',404);
        }
        return $this->successResponse('zone fetched successfully',$zone,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateZoneRequest $request)
    {
        $validator=$request->validated();
        $zone=Zone::find($validator['id']);
        if(empty($zone)){
            return $this->failedResponse('zone not found',404);
        }   
        $zone->update($request->validated());
        return $this->messageResponse("zone is updated successfully",200);
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
        $zone=Zone::find($request->id);
        if(empty($zone)){
            return $this->failedResponse('zone not found',404);
        }
        $zone->delete();
        return $this->messageResponse("zone is deleted successfully",200);
    }
}

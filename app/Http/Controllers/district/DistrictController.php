<?php

namespace App\Http\Controllers\district;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $request)
    {
        $validator=$request->validated();
        $id=null;
        $districts="";
        if(isset($validator['id'])){
            $id=$validator['id'];
        }
        if(is_null($id)){
            $districts=District::all();
        }
        else{
            $districts=District::find($id);
        }
        if(empty($districts)){
            return $this->failedResponse('failed to fetch',404);
        }
        return $this->successResponse('districts  Fetched successfully',$districts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistrictRequest $request)
    {
        $validated=$request->validated();
        $district=District::create($validated);
        return $this->messageResponse('district is added successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistrictRequest $request)
    {
        $validator=$request->validated();
        $district=District::find($validator['id']);
        if(empty($district)){
            return $this->failedResponse('district not found',404);
        }   
        $district->update($request->validated());

        return $this->messageResponse('district is updated successfully',200);
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
        $district=District::find($request->id);
        if(empty($district)){
            return $this->failedResponse('district not found',404);
        }
        $district->delete();
         return $this->messageResponse('district is deleted successfully',200);
    }
}

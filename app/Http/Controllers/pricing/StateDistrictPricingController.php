<?php

namespace App\Http\Controllers\pricing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\StoreStateDistrictPricingRequest;
use App\Http\Requests\StoreStateDPMultiplesRequest;
use App\Http\Requests\UpdateStateDistrictPricingRequest;
use App\Models\StateDistrictPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StateDistrictPricingController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {  
      $validator=Validator::make($request->all(),[
        'state_id'=>'nullable|exists:states,id',
        'entry_date'=>'nullable',
        'end_date'=>'nullable'
      ]);
    if($validator->fails()){
        return response()->json([
            'status'=>0,
            'message'=>$validator->errors()
        ],422);
    }
    if(!is_null($request->state_id)){
          $prices=StateDistrictPricing::where('state_id',$request->state_id)->get();
    }
    elseif(!is_null($request->entry_date)){
        $prices=StateDistrictPricing::where('entry_date',$request->entry_date)->get();
    }
    else{

        $prices=StateDistrictPricing::all();
    }
   
    $formatData=$prices->groupBy('state_id')->map(function($items){
        $items = $items->filter(function ($item) {
            return $item->state && $item->district; 
        });
    
        if ($items->isEmpty()) {
            return null; 
        }
       
        $state=$items->first()->state->name;

        return[
             'date'=>$items->first()->entry_date,
             'time'=>$items->first()->entry_time,
             'state_name'=>$state,
             'districts'=>$items->map(function($item){
                return [
                    "district_name"=>$item->district->name,
                    "prict"=>$item->price
                ];
             })->values()
        ];
    })->values();
    
    return $this->successResponse('data fetched successfully',$formatData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateDistrictPricingRequest $request)
    {
        $pricting=StateDistrictPricing::create($request->validated());
        return $this->messageResponse('state district pricing is added successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
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
        $pricing=StateDistrictPricing::with(['state','district'])->find($request->id);
        return $this->successResponse('state district pricing data fetched successfully',$pricing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateDistrictPricingRequest $request)
    {
        $validated=$request->validated();
        $pricing=StateDistrictPricing::find($validated['id']);
        if(empty($pricing)){
            return $this->failedResponse('not found',404);
        }
        $pricing->update($validated);
        return $this->messageResponse('state district pricing is updated successfully',200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
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

        $pricing=StateDistrictPricing::find($request->id);
        if(empty($pricing)){
            return $this->failedResponse('state not found',404);
        }
        $pricing->delete();
        return $this->messageResponse('state district pricing is deleted successfully',201);
    }

    public function storemultiples(StoreStateDPMultiplesRequest $request){
        $validated=$request->validated();
        foreach($validated['districts'] as $district){
            StateDistrictPricing::create([
              'state_id'=>$validated['state_id'],
              'entry_date'=>$validated['entry_date'],
              'entry_time'=>$validated['entry_time'],
              'district_id'=>$district['district_id'],
              'price'=>$district['price']
            ]);
         }

        return response()->json([
            'status'=>1,
            'message'=>'state districts prices added successfully',
        ],201);

    }
    public function fetchByStateId(Request $request){
        $validator=Validator::make($request->all(),[
            'state_id'=>'nullable|exists:states,id',
            'entry_date'=>'nullable'
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>0,
                'message'=>$validator->errors()
            ],422);
        }
        if(!is_null($request->state_id)){
              $prices=StateDistrictPricing::where('state_id',$request->state_id)->get();
        }
        elseif(!is_null($request->entry_date)){
            $prices=StateDistrictPricing::where('entry_date',$request->entry_date)->get();
        }
        else{

            $prices=StateDistrictPricing::all();
        }
       
        $formatData=$prices->groupBy('entry_date')->map(function($items){
            $items = $items->filter(function ($item) {
                return $item->state && $item->district; 
            });
        
            if ($items->isEmpty()) {
                return null; 
            }
           $state=$items->first()->state->name;

            return[
                 'date'=>$items->first()->entry_date,
                 'time'=>$items->first()->entry_time,
                 'state_name'=>$state,
                 'districts'=>$items->map(function($item){
                    return [
                        "district_name"=>$item->district->name,
                        "prict"=>$item->price
                    ];
                 })->values()
            ];
        })->values();
        
        return $this->successResponse('data fetched successfully',$formatData);
    }

    // public function fetcheByState(Request $request){
        
    // }
}

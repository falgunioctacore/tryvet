<?php

namespace App\Http\Controllers\pricen;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\StorePriceRequest;
use App\Http\Requests\UpdatePriceItemRequest;
use App\Http\Requests\UpdatePriceRequest;
use App\Models\GroupItem;
use App\Models\Price;
use App\Models\PriceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\returnSelf;

class PriceController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator=Validator::make($request->all(),[

            'entry_date'=>'nullable',
            'category_id'=>'nullable|categories,id',
            'main_group_id'=>'nullable|main_groups,id',
          ]);
                 
                // Start query
       $query = Price::with(['priceItems', 'mainGroup']);
       
       // 🧩 Apply filters individually if they exist
       if ($request->filled('entry_date')) {
           $query->where('date', $request->entry_date);
       }
       
       if ($request->filled('category_id')) {
           $query->where('category_id', $request->category_id);
       }
       
       if ($request->filled('main_group_id')) {
           $query->where('main_group_id', $request->main_group_id);
       }
       
      
       $prices = $query->orderByDesc('created_at')->get();
       
       
       if ($prices->isEmpty()) {
           return $this->failedResponse('No prices found', 404);
       }
      
      $data = $prices->groupBy('price_id')->map(function ($group) {
        return $group->map(function ($price) {
            // Check if the state relationship is not null before accessing its name
            // $stateName = $price->state ? $price->state->name : 'Unknown State';
    
            return [
                'id'=>$price->id,
                'main_group_id'=>$price->mainGroup->id??'',
                'main_group'=>$price->mainGroup->name??'',
                'title'=>$price->title,
                'title_id'=>$price->title_id,
                'category_id'=>$price->category_id??"",
                'category_name'=>$price->category->category??'',
                'group_name'=>$price->group->title??'',
                'heading'=>$price->heading,
                'date' => $price->date,
                'time' => $price->time, 
                // 'state_name' => $stateName,
                'group' => $price->priceItems->map(function ($item) {
                   
                        // Check if the district relationship is not null before accessing its name
                        $districtName = $item->district ? $item->district->name : 'Unknown District';
    
                        return [
                            'id'=>$item->id,
                            'district_name' => $districtName,
                            'price' => $item->price // Assuming 'prict' is a column in price_items table
                        ];
    
                })->values()
            ];
        });
    })->values()->flatten(1);


    
    // Return the formatted response
    return $this->successResponse('Data fetched successfully', $data);
    
    }
    public function store(StorePriceRequest $request)
    {
        $validated=$request->validated();
        $price=Price::create([
            'main_group_id'=>$validated['main_group_id'],
            'title'=>$validated['title'],
            'title_id'=>$validated['title_id'],
            'category_id'=>$validated['category_id'],
            'heading'=>$validated['heading'],
            'date'=>$validated['date'],
            'time'=>$validated['time'],
        ]);

        foreach($validated['group'] as $district){
            PriceItem::create([
                'price_id'=>$price->id,
                'district_id'=>$district['district_id'],
                'price'=>$district['price'],
            ]);
        }

        return response()->json([
            'status'=>1,
            'message'=>'state price is created successfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //if ke andar or foreach ke updar rahega ye 
        // $districtIds = collect($validated['districts'])->pluck('district_id')->toArray();

        // // Delete PriceItems that are not in the updated districts
        // PriceItem::where('price_id', $validated['id'])
        //          ->whereNotIn('district_id', $districtIds)
        //          ->delete();
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(UpdatePriceRequest $updatePriceRequest){
            $validated=$updatePriceRequest->validated();
            $price=Price::findOrFail($validated['id']);
            $price->update([
                'main_group_id'=>$validated['main_group_id'],
                'title'=>$validated['title']??$price->title,
                'title_id'=>$validated['title_id'],
                'category_id'=>$validated['category_id'],
                'heading'=>$validated['heading']??$price->heading,
                'date'=>$validated['date']??$price->date,
                'time'=>$validated['time']??$price->time,
            ]);
            if(isset($validated['group'])){
                $districtIds = collect($validated['group'])->pluck('district_id')->toArray();
                // PriceItem::where('price_id',$validated['id'])
                //         ->whereNotIn('district_id',$districtIds)
                //         ->delete();
            foreach($validated['group'] as $district){
                $priceItem=PriceItem::where(['price_id'=>$validated['id'],'district_id'=>$district['district_id']])->first();
                
                if(is_object($priceItem)){
                   $priceItem->update([
                         'price'=>$district['price'],
                   ]);
                 }
                 else{
                   PriceItem::create([
                       'price_id'=>$price->id,
                       'district_id'=>$district['district_id'],
                       'price'=>$district['price'],
                   ]);
                 }
                
            }
          }
            return $this->messageResponse('updated successfully');
     }
    // public function update(UpdatePriceRequest $request)
    // {
    //     $validated=$request->validated();
    //     $price=Price::findOrFail($validated['id']);
    //     $price->update($validated);

    //     return $this->messageResponse('updated success fully',200);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(
                [
                    'status'=>0,
                    'message'=>'something is wrong',
                    'errors'=>$validator->errors()
                ]
            );
        }

        $price=Price::find($request->id);
        if(empty($price)){
            return $this->failedResponse('not found',404);
        }
        $price->delete();
        return $this->messageResponse('deleted successfully');

    }

    public function updatePriceItem(UpdatePriceItemRequest $request){
        $validated=$request->validated();
        $priceItem=PriceItem::find($validated['id']);
        if(empty($priceItem)){
            return $this->failedResponse('not found',404);
        }
        $priceItem->update($request->validated());
        return $this->messageResponse('updated success fully');
    }

    public function deletePriceItem(Request $request){
        $validator=Validator::make($request->all(),[
            'id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>0,
                'error'=>'something is wrong',
                'errors'=>$validator->errors()
            ]);
        }
        $priceItem=PriceItem::find($request->id);
        if(empty($priceItem)){
            return $this->failedResponse('not found',404);
        }
        $priceItem->delete();
        return $this->messageResponse('deleted successfully');
    }
    
}

<?php

namespace App\Http\Controllers\subscription;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\PackageType;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
{
       $query = Subscription::with([
        'user:id,name',
        'packageType:id,name,price',
        'mainGroup:id,name',
    ]);

    // Apply date filters
    if ($request->has('start_date') && $request->has('end_date')) {
        $query->whereBetween('created_at', [
            $request->input('start_date'),
            $request->input('end_date')
        ]);
    }

    // Apply main_group_id filter
    if ($request->has('main_group_id')) {
        $query->where('main_group_id', $request->input('main_group_id'));
    }

    $subscriptions = $query->get();
    // return $subscriptions;
    // 🔁  $subTransform output
    $transformed = $subscriptions->map(function ($item) {
     // // 
        return [
            'id' => $item->id,
            'user_id' => $item->user_id ?? '',
            'image'=>$item->image_url??'',
            'user_name' => $item->user->name ?? '',
            'package_id'=>$item->package_id ??'',
            'package_type' => $item->packageType->name ?? '',
            'package_price'=>$item->packageType->price ?? '',
            'main_group' => $item->mainGroup->name ?? '',
            'main_group_id' => $item->main_group_id ?? '',
            'start_date' => $item->start_date ?? '',
            'end_date' => $item->end_date ?? '',
            'flag'=>$item->flag??'',
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            
        ];
    });

    return $this->successResponse('Data fetched successfully', $transformed, 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request)
    {
         $validated=$request->validated();
        $imagePath=$request->file('image')->store('subscriptions','public');
        // $startDate = Carbon::now()->format('Y-m-d'); 
        $startDate=Carbon::now();

        // end date calculation
        $packageType =PackageType::select(['name'])->find($validated['package_id']);
        $packageType=$packageType->name;
        preg_match('/(\d+)\s*(\w+)/', $packageType, $matches);
        if (count($matches) < 3) {
            return response()->json(['message' => 'Invalid package type format. Example: "1 month", "1 year"'], 400);
        }
        $duration = (int) $matches[1];
        $unit = strtolower($matches[2]);
  
        // Make sure the unit is valid
        if (!in_array($unit, ['day', 'month', 'year'])) {
            return response()->json(['message' => 'Invalid package type unit. Use "day", "month", or "year".'], 400);
        }
  
        $endDate = $startDate->copy()->add($duration, $unit);
        // end calculation of end date
        // $validated['image']=public_path("storage/".$imagePath);
        $subscription=Subscription::create([
            'user_id'=>$validated['user_id'],
            'package_id'=>$validated['package_id'],
            // 'price'=>$validated['price'],
            'start_date'=>$startDate->format('Y-m-d'),
            'end_date'=>$endDate->format('Y-m-d'),
            'image_url'=>$imagePath,
            'flag'=>0,
            'main_group_id'=>$validated['main_group_id'],
        ]);
        return $this->messageResponse('subcription is added successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>0,
                'validation'=>$validator->errors(),
            ]);
        }
        $subscription=Subscription::with(['user:id,name','packageType:id,name'])->find($request->id);
    //     $subscription = DB::table('subscriptions')
    // ->join('users', 'subscriptions.user_id', '=', 'users.id')
    // ->join('package_types', 'subscriptions.package_id', '=', 'package_types.id')
    // ->select(
    //     'subscriptions.id',
    //     'subscriptions.user_id',
    //     'subscriptions.package_id',
    //     'subscriptions.image_url',
    //     'subscriptions.flag',
    //     'subscriptions.deleted_at',
    //     'subscriptions.created_at',
    //     'subscriptions.updated_at',
    //     'users.name as user_name',  // Adding 'name' from users
    //     'package_types.name as package_name' // Adding 'name' from package_types
    // )
    // ->where('subscriptions.id', $request->id)
    // ->first();

        if(empty($subscription)){
            return $this->failedResponse('not found',404);
        }
        $subscription->image_url=asset('storage/'.$subscription->image_url);
        return response()->json([
            'status'=>1,
            'message'=>'data is fetched success fully',
            'data'=>$subscription,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateSubscriptionRequest $request)
    // {
    //     $validated=$request->validated();
    //     $subscription=Subscription::find($validated['id']);
    //     if(empty($subscription)){
    //         return $this->failedResponse('subscripton not found',404);
    //     }
    //     $packageType = $validated['package_type'];
    //     preg_match('/(\d+)\s*(\w+)/', $packageType, $matches);
    //     if (count($matches) < 3) {
    //         return response()->json(['message' => 'Invalid package type format. Example: "1 month", "1 year"'], 400);
    //     }
    //     $duration = (int) $matches[1];
    //     $unit = strtolower($matches[2]);

    //     // Make sure the unit is valid
    //     if (!in_array($unit, ['day', 'month', 'year'])) {
    //         return response()->json(['message' => 'Invalid package type unit. Use "day", "month", or "year".'], 400);
    //     }

    //     $startDate = Carbon::parse($validated['start_date']);
    //     $endDate = $startDate->copy()->add($duration, $unit); 

    //     $subscription->update([
    //         'start_date' => $validated['start_date'],
    //         'end_date'   => $endDate->toDateString(), // Store the end date as a string
    //         'flag'        => $validated['flag'],
    //     ]);
    //     return $this->messageResponse('subscription is updated successfully');
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
            return response()->json([
                'status'=>0,
                'validation'=>$validator->errors(),
            ]);
        }
        $subscription=Subscription::find($request->id);

        if(empty($subscription)){
            return $this->failedResponse('not found',404);
        }
        $subscription->delete();
        return $this->messageResponse('subscription is deleted successfully');
    }

   public function checkSubscriptionUserById(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer',  // Ensuring the user_id is an integer
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'message' => $validator->errors(),
        ], 422);
    }

    // Fetch the subscription data
    $subscription = Subscription::with([
        'user:id,name',
        'packageType:id,name,price',
        'mainGroup',
    ])
    ->where('user_id', $request->user_id)
    ->latest('start_date')
    ->first(['flag', 'user_id', 'package_id', 'start_date', 'end_date','main_group_id']);

    // Check if the subscription was found
    if (!$subscription) {
        return $this->failedResponseWithData('Not Found', [
            "flag" => '',
            "user_id" => '',
            "package_id" => '',
            "start_date" => "",
            "end_date" => "",
            "user_name" => "",
            "package_name" => "",
            "package_price" => ""
        ], 200);
    }

    // Transform the subscription data to include additional information
    $subscription->user_name = $subscription->user->name ?? '';  // Safely access user data
    $subscription->package_name = $subscription->packageType->name ?? '';  // Safely access package data
    $subscription->package_price = $subscription->packageType->price ?? '';  // Safely access price
    $subscription->main_group=$subscription->mainGroup->name ?? '';
    $subscription->main_group_id=$subscription->main_group_id ??'';
    $subscription->start_date=$subscription->start_date ??'';

    // Unset unnecessary attributes
    unset($subscription->user, $subscription->packageType,$subscription->mainGroup);

    // Return the transformed data
    return $this->successResponse('Data fetched successfully', $subscription, 200);
}

  //   new Updated functions 

  public function update(UpdateSubscriptionRequest $request)
  {
      $validated=$request->validated();
      $subscription=Subscription::find($validated['id']);
      if(empty($subscription)){
          return $this->failedResponse('subscripton not found',404);
      }
      $packageType =PackageType::select(['name'])->find($validated['package_id']);
      $packageType=$packageType->name;
      preg_match('/(\d+)\s*(\w+)/', $packageType, $matches);
      if (count($matches) < 3) {
          return response()->json(['message' => 'Invalid package type format. Example: "1 month", "1 year"'], 400);
      }
      $duration = (int) $matches[1];
      $unit = strtolower($matches[2]);

      // Make sure the unit is valid
      if (!in_array($unit, ['day', 'month', 'year'])) {
          return response()->json(['message' => 'Invalid package type unit. Use "day", "month", or "year".'], 400);
      }

      $startDate = Carbon::parse($validated['start_date']);
      $endDate = $startDate->copy()->add($duration, $unit); 

      $subscription->update([
          'start_date' => $validated['start_date'],
          'end_date'   => $endDate->toDateString(), // Store the end date as a string
          'flag'        => $validated['flag'],
          'package_id'=>$validated['package_id'],
          'main_group_id'  =>$validated['main_group_id']??$subscription->main_group_id,
          
      ]);
      return $this->messageResponse('subscription is updated successfully');
  }

      
}

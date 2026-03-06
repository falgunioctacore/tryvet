<?php

namespace App\Http\Controllers\package;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdPackageTypeRequest;
use App\Http\Requests\PackageTypeRequest;
use App\Http\Requests\PackageTypeUpdateRequest;
use App\Http\Requests\UpdatePackageTypeRequest;
use App\Models\PackageType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageTypeController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdPackageTypeRequest $request)
    {
         $validator = $request->validated();
    $id = $validator['id'] ?? null;
    $mainGroupId = $validator['main_group_id'] ?? null;

    $package = collect();

    if (is_null($id) && is_null($mainGroupId)) {
        $package = PackageType::with('mainGroup')->get();
    } else if (!is_null($mainGroupId)) {
        $package = PackageType::with('mainGroup')
            ->where('main_group_id', $mainGroupId)
            ->get();
    } else {
        $single = PackageType::with('mainGroup')->find($id);
        if ($single) {
            $package = collect([$single]);
        }
    }

    if ($package->isEmpty()) {
        return $this->failedResponse("package type not found", 404);
    }

    // 🔁 Transform each package
    $transformed = $package->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'deleted_at' => $item->deleted_at,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'main_group_id' => $item->main_group_id,
            'main_group' => $item->mainGroup->name ?? null,
        ];
    });

    return $this->successResponse('package type is successfully fetched', $transformed, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageTypeRequest $request)
    {
        $package=PackageType::create($request->validated());
        return response()->json([
          "status"=>1,
          'message'=>'package is added successfully'
          ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package=PackageType::find($id);
        if(empty($package)){
            return response()->json([
                "status"=>0,
                'message'=>'not found'],404);
        }
        return response()->json(['package_type'=>$package]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageTypeRequest $request)
    {
        $validator=$request->validated();
        $id=$validator['id'];
        $package=PackageType::find($id);
        if(empty($package)){
            return response()->json([
                "status"=>0,
                'message'=>'not found'],404);
        }

        $package->update($request->validated());

        return response()->json([
            "status"=>1,
            'message'=>'package type is updated successfully!'
             
        ],202);

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
        $package=PackageType::find($request->id);
    
        if(empty($package)){
            return response()->json([
                'status'=>0,
                'message'=>'not found'],404);
        }
        $package->delete();
        return response()->json([
            "status"=>1,
            'message'=>'package type is deleted successfully'],200);
    }
}

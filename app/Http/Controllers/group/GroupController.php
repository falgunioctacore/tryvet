<?php

namespace App\Http\Controllers\group;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;
use App\Models\GroupItem;
use App\Models\MainGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'id'=>'nullable|exists:groups,id',
            'main_group_id'=>'nullable|exists:main_groups,id',
          ]);

          if($validator->fails()){
           return $this->failedResponse('Not Found');
          }

          if(!is_null($request->main_group_id)){
            $groups=Group::with(['groupItems','groupItems.district','mainGroup'])->where('main_group_id','=',$request->main_group_id)->orderByDesc('created_at')->get();
            $formattedGroups = $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'title' => $group->title,
                    'main_group'=>$group->mainGroup->name??'',
                    'main_group_id'=>$group->main_group_id??'',
                    'deleted_at' => $group->deleted_at, // If you want to keep 'deleted_at' field
                    'created_at' => $group->created_at->toISOString(),
                    'updated_at' => $group->updated_at->toISOString(),
                    'group' => $group->groupItems->map(function ($groupItem) {
                        return [
                            'id' => $groupItem->id,
                            'group_id' => $groupItem->group_id,
                            'disctrict_id'=> $groupItem->district_id,
                            'district' => $groupItem->district->name??'Unknown' // Just return the district name as a string
                        ];
                    })
                ];
            });
          }
          elseif(!is_null($request->id)){
            $groups=Group::with(['groupItems','groupItems.district','mainGroup'])->find($request->id);

            $formattedGroups = [
                'id' => $groups->id,
                'title' => $groups->title,
                'main_group'=>$groups->mainGroup->name??'',
                'main_group_id'=>$groups->main_group_id??'',
                'deleted_at' => $groups->deleted_at, // If you want to keep 'deleted_at' field
                'created_at' => $groups->created_at->toISOString(),
                'updated_at' => $groups->updated_at->toISOString(),
                'group_items' => $groups->groupItems->map(function ($groupItem) {
                    return [
                        'id' => $groupItem->id,
                        'group_id' => $groupItem->group_id,
                        'district_id'=>$groupItem->district_id,
                        'district' => $groupItem->district->name??'Unkown',  
                    ];
                })
            ];
         }
         else{
            $groups=Group::with(['groupItems','groupItems.district','mainGroup'])->orderByDesc('created_at')->get();
            $formattedGroups = $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'title' => $group->title,
                    'main_group'=>$group->mainGroup->name??'',
                    'main_group_id'=>$group->main_group_id??'',
                    'deleted_at' => $group->deleted_at, // If you want to keep 'deleted_at' field
                    'created_at' => $group->created_at->toISOString(),
                    'updated_at' => $group->updated_at->toISOString(),
                    'group' => $group->groupItems->map(function ($groupItem) {
                        return [
                            'id' => $groupItem->id,
                            'group_id' => $groupItem->group_id,
                            'disctrict_id'=> $groupItem->district_id,
                            'district' => $groupItem->district->name??'Unknown' // Just return the district name as a string
                        ];
                    })
                ];
            });
            
         }

    

         return $this->successResponse('data fethced successfully',$formattedGroups,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $validated=$request->validated();
        $groupN=Group::create([
            'title'=>$validated['title'],
            'main_group_id'=>$validated['main_group_id']
        ]);
    
        foreach($validated['group'] as $group){
            GroupItem::create([
                'group_id'=>$groupN->id,
                'district_id'=>$group['district_id'],
            ]);
        }

      return $this->messageResponse('group is successfully created',201);
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
    public function update(UpdateGroupRequest $request)
    {
        $validated=$request->validated();
        $groupU=Group::find($validated['id']);
        $groupU->update([
            'main_group_id'=>$validated['main_group_id']??$groupU->main_group_id,
            'title'=>$validated['title']??$groupU->title,
            ]);
        if(!is_object($groupU)){
            return $this->failedResponse('not found',404);
        }
    
        if(isset($validated['group'])){
            $districtIds = collect($validated['group'])->pluck('district_id')->toArray();
            GroupItem::where('group_id',$validated['id'])
                    ->whereNotIn('district_id',$districtIds)
                    ->delete();
            foreach($validated['group'] as $group){
                $groupItem=GroupItem::where(['group_id'=>$validated['id'],'district_id'=>$group['district_id']])->first();
                if(is_object($groupItem)){
                    $groupItem->update([
                        'district_id'=>$group['district_id']
                    ]);
                }
            else{
               GroupItem::create([
                'group_id'=>$groupU->id,
                'district_id'=>$group['district_id'],
               ]);
             }
            }
        }
        return $this->messageResponse('Group is updated successfully now',200);
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

        $group=Group::find($request->id);
        if(empty($group)){
            $this->failedResponse('not found ',404);
        }
       $group->delete();
       return $this->messageResponse('deleted successfully',200);
    }
}

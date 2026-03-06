<?php

namespace App\Http\Controllers\news;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\NewsIdRequest;
use App\Http\Requests\NewsRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NewsController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(NewsIdRequest $request)
    {
       
        $validatore=$request->validated();
        // return $validatore;
        $id=null;
        if(isset($validatore['id'])){
            $id=$validatore['id'];
        }
        
        // return $id;
        if(!is_null($id)){
            $news=News::find($id);
            // $news=News::all();
        }
        else{
            $news=News::all();
        }
         if(empty($news)){
            return response()->json([
                "status"=>0,
                'message'=>'not found'
            ],404);
         }
         else{
           return $this->successResponse('news fetched successfully',$news,200);       
          }
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsRequest $request)
    {
        $data=$request->validated();
        $data['user_id']=1;
    
        $news=News::create($data);

        if(!empty($news)){
          return response()->json([
            "status"=>1,
             "message"=>'new is added successfully'
          ],201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsIdRequest $request)
    {
         $validatore=$request->validated();
         $news=News::find($validatore['id']);
         if(empty($news)){
            return response()->json([
                "status"=>0,
                'message'=>'not found'
            ],404);
         }
         else{
            return response()->json([
                "status"=>1,
                'news'=>$news],200);
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsUpdateRequest $request,$id)
    {
        $validator=$request->validated();
        $news=News::find($validator['id']);
        if(empty($news)){
            return response()->json(['message'=>'not found'],404);
        }

        $news->update($request->validated());
        return response()->json([
            "status"=>1,
            'message'=>"news is updated successfully"
        ],200);
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
        $news=News::find($request->id);
        if(empty($news)){
            return response()->json([
                'status'=>0,
                'message'=>'not found'],404);
        }   

        $news->delete();
        return response()->json([
            "status"=>1,
            'message'=>'news is deleted successfully',
        ],200);
    }

    public function news_update(NewsUpdateRequest $request)
    {
        $validator=$request->validated();
        $news=News::find($validator['id']);
        if(empty($news)){
            return response()->json(['message'=>'not found'],404);
        }

        $news->update($request->validated());
        return response()->json([
            "status"=>1,
            'message'=>"news is updated successfully"
        ],200);
    }

}

<?php

namespace App\Http\Controllers\category;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdCategoryRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdCategoryRequest $request)
    {
        $validator=$request->validated();
        $id=null;
        // return $validator;
        if(isset($validator['id'])){
            $id=$validator['id'];
        }
        $category="";
        if($id==null){
           $category=Category::all();
        }
        else{
            $category=Category::find($id);
        }
        if(empty($category)){
            return $this->failedResponse("category  not found",404);
        }
       
            return $this->successResponse('category  is successfully fetched',$category,200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated=$request->validated();

        Category::create($validated);
        return $this->messageResponse('category is added success fully',201);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request)
    {
        $validated=$request->validated();
        $category=Category::find($validated['id']);
        if(empty($category)){
           return $this->failedResponse('not found',404);
        }
        $category->update($validated);
        return $this->messageResponse('category is updated successfully',200);
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

        $category=Category::find($request->id);
        if(empty($category)){
              return $this->failedResponse('category not found',404);
        }
        $category->delete();

        return $this->messageResponse('category is deleted successfully',200);
    }
}

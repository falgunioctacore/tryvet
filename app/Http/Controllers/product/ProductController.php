<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $request)
    {
        $validator=$request->validated();
        $id=null;
        if(isset($validator['id'])){
        $id=$validator['id'];
        }
        $products='';
        if(is_null($id)){
           $products=Product::all();
        }
        else{
            $products=Product::find($id);
        }
   
        if(empty($products)){
            return $this->failedResponse('not found data',404);
        }
         return $this->successResponse('product data fetched successfully',$products,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $imagePath = null;
        $products=$request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $products['image']=public_path("storage/".$imagePath);
        }
        // $product = Product::create([
        //     'name' => $request->name,
        //     'descriptions' => $request->description,
        //     'price' => $request->price,
        //     'image' => $imagePath,
        // ]);
        Product::create($products);
        
        return $this->messageResponse('product is added successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product=Product::find($id);

        if(!$product){
            return response()->json([
                "status"=>0,
                'message'=>'product not found'],404);
        }
        
        return response()->json([
             "status"=>1,
            "product"=>$product],200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request)
    {
        $validadtor=$request->validated();
        $product=Product::find($validadtor['id']);
        $updateProudct=$request->validated();
        // $updateProudct=$request->validated();
        // return response()->json($updateProudct);
        if(!$product){
            return response()->json(['message'=>'product is not found'],404);
        }

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            // $imagePath=public_path("storage/".$product->image);
            if(file_exists($product->image)){
                @unlink($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $updateProudct['image']=public_path($imagePath);
        }

        $product->update($updateProudct);
        return response()->json([
            'status'=>1,
            'message'=>'product is updated successfully',
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request){

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

        $product=Product::find($request->id);
        if(!$product){
            return response()->json([
                'status'=>0,
                'message'=>'not found'],404);
        }

        if($product->image){

            // $imagePath=public_path("storage/".$product->image);
            if(file_exists($product->image)){
                @unlink($product->image);
            }
        }
         
        $product->delete();
        
        return response()->json([
            'status'=>1,
            'message'=>'product is deleted successfully'],200);
    }
}

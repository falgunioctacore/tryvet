<?php

namespace App\Http\Controllers\qrcode;

use App\Http\Controllers\Controller;
use App\Http\Controllers\response\ResponseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\QRImageStoreRequest;
use App\Http\Requests\QRImageUpdateRequest;
use App\Models\QRCode;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Support\Facades\Validator;

class QRImageController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(IdRequest $idRequest)
    {
       $validator = $idRequest->validated();
    $id = $validator['id'] ?? null;
    $mainGroupId=$validator['main_group_id'] ?? null;

    if (is_null($id) && is_null($mainGroupId)) {
        $qrCodes = QRCode::with('mainGroup')->get();
    } 
    else if(!is_null($mainGroupId)){
        $qrCodes = QRCode::with('mainGroup')->where('main_group_id',$mainGroupId)->get();
    }
    else {
        $record = QRCode::with('mainGroup')->find($id);
        $qrCodes = $record ? collect([$record]) : collect();
    }

    if ($qrCodes->isEmpty()) {
        return $this->failedResponse('Not found QR codes', 404,$this->emptyData());
    }

    $transformed = $qrCodes->map(function ($item) {
        return [
            'id' => $item->id,
            'qr_image' => asset('storage/' . $item->qr_image),
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'main_group_id' => $item->main_group_id,
            'main_group' => $item->mainGroup->name ?? null,
        ];
    });

    return $this->successResponse('Fetched all QR codes', $transformed, 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QRImageStoreRequest $request)
    {
        $validator=$request->validated();
        $imagePath=$request->file('qr_image')->store('qr_images','public');
        // $imagePath=public_path("storage/".$imagePath);
        $qrImage=QRCode::create(['main_group_id'=>$validator['main_group_id'],'qr_image'=>$imagePath]);
        return $this->messageResponse('created successfully',200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $qrImage=QRCode::with('mainGroup')->find($id);
        if(empty($qrImage)){
            return $this->failedResponse('qr image not found',404);
        }
        else{
            $filePath=public_path('storage/').$qrImage->qr_image;
            // return response()->json([$filePath]);
            if(!file_exists($filePath)){
                return $this->failedResponse('You are not upload image',404);
            }
            // return response()->stream(function() use ($filePath){
            //    $image=fopen($filePath,'rb');
            //    fpassthru($image);

            // },200, ['Content-Type'=>mime_content_type($filePath)]);
            //   \Log::info('Serving image: ' . $filePath);

              return $this->successResponse('successfully fetched image path',[
                // 'image_url'=>$filePath,
                // 'main_group'=>$qrImage->mainGroup->name??'',
                // 'main_group_id'=>$qrImage->main_group_id??'',
                
              ],200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QRImageUpdateRequest $request)
    {
        $validator=$request->validated();
        $qrCode=QRCode::find($validator['id']);
        if(empty($qrCode)){
            return $this->failedResponse('not found image',404);
        }

        if ($request->hasFile('qr_image')) {
            // Delete the old image if it exists
            $imagePath=public_path("storage/".$qrCode->qr_image);
            if(file_exists($imagePath)){
                @unlink($imagePath);
            }
            
            $imagePath=$request->file('qr_image')->store('qr_images','public');
        }
       
        // $imagePath=public_path("storage/".$imagePath);
        $validator['qr_image']=$imagePath ?? $qrCode->qr_image;
        if(!isset($validator['main_group_id'])||is_null($validator['main_group_id'])){
            $validator['main_group_id']=$qrCode->main_group_id;
        }
        $qrCode->update($validator);
        return $this->messageResponse('updated QRCode image successfully',200);
        
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
        $qrCode=QRCode::find($request->id);
        if(empty($qrCode)){
            return $this->failedResponse('not found image',404);
        }
            // Delete the old image if it exists
            // $imagePath=public_path("storage/".$qrCode->qr_image);
            if(file_exists($qrCode->qr_image)){
                @unlink($qrCode->qr_image);
            }  
            $qrCode->delete();
            return $this->messageResponse('qr code is deleted ',200);
    }
    
      public function emptyData() {
      $data = [
         [
            "id" => "",
            "qr_image" => "",
            "created_at" => "",
            "updated_at" => "",
            "main_group_id" => "",
            "main_group" => ""
        ]
    ];

    return  $data;
 }
}

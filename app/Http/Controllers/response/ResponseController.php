<?php

namespace App\Http\Controllers\response;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function successResponse($message,$data,$status=200){
        return response()->json([
            'status'=>1,
            "message"=>$message,
            "data"=>$data,
        ], $status);
    }

    public function failedResponse($message,$status=400,$data=[]){
            return response()->json([
                'status'=>0,
                'message'=>$message,
                'data'=>$data,
            ],$status);
    }

    public function messageResponse($message,$status=200){
        return response()->json([
            'status'=>1,
            'message'=>$message,
        ],$status);
    }
    
     public function failedResponseWithData($message,$data,$status=400){
        return response()->json([
            'status'=>0,
            "message"=>$message,
            "data"=>$data,
        ], $status);
    }
    
}

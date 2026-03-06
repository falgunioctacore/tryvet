<?php

use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\district\DistrictController;
use App\Http\Controllers\group\GroupController;
use App\Http\Controllers\group\MainGroupController;
use App\Http\Controllers\news\NewsController;
use App\Http\Controllers\notification\NotificationController;
use App\Http\Controllers\package\PackageTypeController;
use App\Http\Controllers\pricen\PriceController;
use App\Http\Controllers\pricing\StateDistrictPricingController;
use App\Http\Controllers\product\ProductController;
use App\Http\Controllers\qrcode\QRImageController;
use App\Http\Controllers\razor\RazorPaymentController;
use App\Http\Controllers\renew\PlanRenewController;
use App\Http\Controllers\spatie\RolePermissionController;
use App\Http\Controllers\state\StateController;
// use App\Http\Controllers\spatie\;
use App\Http\Controllers\subscription\SubscriptionController;
use App\Http\Controllers\zone\ZoneController;
use App\Models\State;
use App\Models\StateDistrictPricing;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register'])->middleware('replace.nulls');
Route::post('login', [AuthController::class, 'login'])->middleware('replace.nulls');
 
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware(['replace.nulls'])->group(function(){
   Route::prefix('/admin')->group(function(){
       Route::apiResource('/user',UserController::class,[
        'except'=>['show','update','delete']
       ]);

       Route::put('/user',[UserController::class,'update']);
    
       Route::delete('/user',[UserController::class,'destroy']);
       Route::get('/user/filter-by-end-date',[UserController::class,'expiredOrExpiringUsers']);
    
   });

    Route::controller(AuthController::class)->group(function(){
        Route::post('logout','logout');
        Route::get('userdetail','userDetail')->middleware('auth:sanctum');
    });

    Route::apiResource('product',ProductController::class,[
        'except'=>['show','update','delete']
    ]);
    Route::put('/product',[ProductController::class,'update']);
    Route::delete('/product',[ProductController::class,'destroy']);
    Route::apiResource('news',NewsController::class,[
       "except"=>['show','update','delete','store'],
    ]);
    Route::post('/news/save',[NewsController::class,'store']);
    Route::put('/news/update',[NewsController::class,'news_update']);
    Route::delete('/news',[NewsController::class,'destroy']);
    
    Route::get('/package-type/index',[PackageTypeController::class,'index']);
    Route::post('/package-type/save',[PackageTypeController::class,'store']);
    Route::post('package-type/update',[PackageTypeController::class,'update']);
    Route::delete('/package-type/delete',[PackageTypeController::class,'destroy']);

    Route::apiResource('plan-renew', PlanRenewController::class,[
        "except"=>['show','update','delete'],
    ]);
    Route::put('/plan-renew',[PlanRenewController::class,'update']);
    Route::delete('/plan-renew',[PlanRenewController::class,'destroy']);
    Route::apiResource('qr-code-image',QRImageController::class,[
        'except'=>['show','update','delete']
    ]);
    Route::put('qr-code-image',[QRImageController::class,'update']);
    Route::delete('qr-code-image',[QRImageController::class,'destroy']);
    Route::apiResource('/zone',ZoneController::class,[
        'except'=>['show','update','destroy']
    ]);
    Route::put('/zone',[ZoneController::class,'update']);
    Route::delete('/zone',[ZoneController::class,'destroy']);
    Route::apiResource('/notification',NotificationController::class,[
        'except'=>['show','update']
    ]);
    Route::put('/notification',[NotificationController::class,'update']);
    Route::delete('notification',[NotificationController::class,'destroy']);
   
    // Route::get('/home',function(){
    //     return response()->json('welcome to home',200);
    // });

    Route::controller(RolePermissionController::class)->group(function(){
        Route::post('role',  'createRole');
        Route::post('permission',  'createPermission');
        Route::post('assign-role',  'assignRoleToUser');
        Route::post('assign-permission',  'assignPermissionToRole');
    });

    Route::apiResource('/state',StateController::class,[
        'except'=>['show','update','delete']
    ]);
    Route::put('/state/update',[StateController::class,'update']);
    Route::delete('/state/delete',[StateController::class,'delete']);

    Route::get('/state-with-districts',[StateController::class,'statewithDistricts']);
    Route::get('/all-states-with-districts',[StateController::class,'statesWithDistricts']);

    Route::apiResource('/district',DistrictController::class,[
        'except'=>['show','update','delete']
    ]);

    Route::put('/district/update',[DistrictController::class,'update']);
    Route::delete('/districe/delete',[DistrictController::class,'delete']);
    //statedistrict pricing
    // Route::get('/state-district-pricing/index',[StateDistrictPricingController::class,'index']);
    // Route::post('/state-district-pricing/store',[StateDistrictPricingController::class,'store']);
    // Route::post('/state-district-pricing/show',[StateDistrictPricingController::class,'show']);
    // Route::post('/state-district-pricing/update',[StateDistrictPricingController::class,'update']);
    // Route::post('/state-district-pricing/delete',[StateDistrictPricingController::class,'delete']);

    // Route::post('/state-district-pricing/save',[StateDistrictPricingController::class,'storemultiples']);
    // Route::post('/state-district-pricing/get-by-state',[StateDistrictPricingController::class,'fetchByStateId']);
    Route::get('/state-district-pricing/index',[PriceController::class,'index']);
    Route::post('/state-district-pricing/save',[PriceController::class,'store']);
    Route::post('/state-district-pricing/show',[PriceController::class,'show']);
    Route::post('/state-district-pricing/update',[PriceController::class,'update']);
    Route::post('/state-district-pricing/delete',[PriceController::class,'destroy']);
    Route::post('/state-district-pricing/child/update',[PriceController::class,'updatePriceItem']);
    Route::post('/state-district-pricing/child/delete',[PriceController::class,'deletePriceItem']);


    // Route::apiResource('/state-price',PriceController::class);

    Route::prefix('/subscription')->controller(SubscriptionController::class)->group(function(){
        Route::get('/index','index');
        Route::post('/show','show');
        Route::post('/delete','destroy');
        Route::post('/save','store');
        Route::post('/update','update');
        Route::get('/check','checkSubscriptionUserById');
    });

    //Group's Api
    Route::prefix('/group')->controller(GroupController::class)->group(function(){
       Route::post('/save','store');
       Route::get('/index','index');
       Route::post('/delete','destroy');
       Route::post('/update','update');
    });
    
      Route::prefix('/category')->controller(CategoryController::class)->group(function(){
         Route::get('/index','index');
         Route::post('/save','store');
         Route::post('/update','update');
         Route::post('/delete','destroy');
    });
    
      Route::prefix('/main-group')->controller(MainGroupController::class)->group(function(){
         Route::get('/index','index');
         Route::post('/save','store');
         Route::post('/update','update');
         Route::post('/delete','destroy');
         
    });
    
    Route::post('/create-order', [RazorPaymentController::class, 'createOrder']);
    Route::post('/verify-payment', [RazorPaymentController::class, 'verify']);
});
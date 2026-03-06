<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'replace.nulls' => \App\Http\Middleware\ReplaceNullWithEmptyString::class,
            'custom.auth'=>\App\Http\Middleware\CustomAuthenticate::class,
            'role'=>\Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'=>\Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (AccessDeniedHttpException $e){
              return response()->json([
                'status'=>0,
                'message'=>'This Action is unauthorize',
              ],403);
        });

        $exceptions->renderable(function (PermissionAlreadyExists $p){
            return response()->json([
                'status'=>0,
                'message'=>'this permission is already exists'
            ],200);
        });
        $exceptions->renderable(function(\Illuminate\Auth\AuthenticationException $e, $request){
                 if($request->is('api/*')){
                    return response()->json([
                        'status'=>0,
                        'message' => 'Unauthenticated'
                    ], 401);
                 }
        });
        $exceptions->renderable(function(\Spatie\Permission\Exceptions\UnauthorizedException $e){
                 return response()->json([
                    'status'=>0,
                    'message'=>'not right perimission',
                 ]);
        });
        $exceptions->renderable(function(\Spatie\Permission\Exceptions\RoleAlreadyExists $roleAlreadyExists){
            return response()->json([
                'status'=>0,
                'message'=>'this role is already exists',
             ]);
        });
    })->create();

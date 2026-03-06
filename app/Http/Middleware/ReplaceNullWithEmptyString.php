<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReplaceNullWithEmptyString
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
  
        if($request->isMethod('get')||$request->isMethod("PUT")||$request->isMethod("POST")){
            $response= $next($request);
            if ($response->headers->get('Content-Type') === 'application/json') {

                $data = json_decode($response->getContent(), true);

                $data = $this->replaceNullsWithEmptyString($data);

                $response->setContent(json_encode($data));

            }
            return $response;
        }
        return $next($request);
    }
     /**
     * Recursively replace null values with empty strings in an array.
     *
     * @param  array  $data
     * @return array
     */
    private function replaceNullsWithEmptyString(array $data)
    {
        return array_map(function ($value) {
            return $value === null ? '' : (is_array($value) ? $this->replaceNullsWithEmptyString($value) : $value);
        }, $data);
    }
}

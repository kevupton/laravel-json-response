<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 28/11/2017
 * Time: 10:05 PM
 */

namespace Kevupton\LaravelJsonResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Kevupton\LaravelJsonResponse\Traits\HasJson;

class CatchValidationExceptions extends OutputJsonResponse
{
    use HasJson;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle (Request $request, Closure $next)
    {
        $json = $this->json();

        /** @var \Illuminate\Http\Response $response */
        try {
            return parent::handle($request, $next);
        } catch (ValidationException $e) {
            $json
                ->mergeErrors($e->errors())
                ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->makeJsonResponse();
    }
}
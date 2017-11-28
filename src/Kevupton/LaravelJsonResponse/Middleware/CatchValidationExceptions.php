<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 28/11/2017
 * Time: 10:05 PM
 */

namespace Kevupton\LaravelJsonResponse\Middleware;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CatchValidationExceptions extends OutputJsonResponse
{
    /**
     * @param ValidationException $e
     * @return bool
     */
    public function handleValidationException (ValidationException $e)
    {
        $this->json()
            ->mergeErrors($e->errors())
            ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        return false;
    }
}
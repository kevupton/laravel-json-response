<?php

namespace Kevupton\LaravelJsonResponse\Middleware;

use Illuminate\Http\Response;

class CatchAllExceptions extends OutputJsonResponse
{
    /**
     * @param \Exception $e
     * @return bool
     */
    public function handleException (\Exception $e)
    {
        $json = $this->json()
            ->error($e->getMessage());

        if (env('APP_DEBUG')) {
            $json->addErrors($e->getTrace());
        }

        $json->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

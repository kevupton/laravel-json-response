<?php

namespace Kevupton\LaravelJsonResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CatchAllExceptions extends CatchValidationExceptions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle (Request $request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        try {
            return parent::handle($request, $next);
        } catch (\Exception $e) {
            $json = $this->json()
                ->error($e->getMessage());

            if (env('APP_DEBUG')) {
                $json->addErrors($e->getTrace());
            }

            $json->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $this->makeJsonResponse();
    }
}

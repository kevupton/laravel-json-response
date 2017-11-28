<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 28/11/2017
 * Time: 10:16 PM
 */

namespace Kevupton\LaravelJsonResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Kevupton\LaravelJsonResponse\Exceptions\JsonResponseErrorException;
use Kevupton\LaravelJsonResponse\Traits\HasJson;

class OutputJsonResponse
{
    use HasJson;

    const AUTH_HEADER = 'Authorization';

    /** @var  Response */
    private $_response;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle (Request $request, Closure $next)
    {
        try {
            $this->_response = $next($request);
        }
        catch (JsonResponseErrorException $e) {
            $this->json()
                ->error($e->getKey(), $e->getValue())
                ->setStatusCode($e->getCode());
        }

        return $this->makeJsonResponse();
    }

    /**
     * Transform the JsonResponse object into an actual Response.
     * Merges headers and original content from the original response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function makeJsonResponse ()
    {
        $_response = $this->_response;
        $headers = [];

        if ($_response) {
            $this->json()->merge($_response->getOriginalContent());
            $headers = $_response->headers;
        }

        if (
            !$_response->exception &&
            $_response->headers->has(self::AUTH_HEADER) &&
            ($headerToken = $_response->headers->get(self::AUTH_HEADER))
        ) {
            $headers[self::AUTH_HEADER] = $headerToken;
        }

        $response = response()
            ->json($this->responseArray(), $this->getStatusCode(), $headers);

        if ($this->hasToken()) {
            $response->header(self::AUTH_HEADER, 'Bearer ' . $this->json()->getToken());
        }

        return $response;
    }
}
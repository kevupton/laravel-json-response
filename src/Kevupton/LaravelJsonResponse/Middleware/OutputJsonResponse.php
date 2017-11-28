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
use ReflectionClass;

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
        $this->_response = $next($request);

        if ($this->_response->exception && $this->passException($this->_response->exception)) {
            return $this->_response;
        }

        return $this->makeJsonResponse();
    }

    /**
     * Sends the exception to the appropriate method.
     *
     * @param \Exception $e
     * @return bool
     */
    private function passException (\Exception $e)
    {
        $reflect = new ReflectionClass($e);
        $method = camel_case('handle' . $reflect->getShortName());

        if (method_exists($this, $method)) {
            return $this->$method($e);
        }
        else if (method_exists($this, 'handleException')) {
            return $this->handleException($e);
        }

        return true;
    }

    /**
     * Handles the json response error
     *
     * @param JsonResponseErrorException $e
     * @return bool
     */
    protected function handleJsonResponseErrorException (JsonResponseErrorException $e) {
        $this->json()
            ->error($e->getKey(), $e->getValue())
            ->setStatusCode($e->getCode());

        return false;
    }

    /**
     * Transform the JsonResponse object into an actual Response.
     * Merges headers and original content from the original response
     *
     * @return Response
     */
    protected function makeJsonResponse ()
    {
        $_response = $this->_response;
        $headers = [];

        if ($_response && !$_response->exception) {
            if ($content = $_response->getOriginalContent()) {
                $this->json()->merge($content);
            }

            if ($_response->headers->has(self::AUTH_HEADER) &&
                ($headerToken = $_response->headers->get(self::AUTH_HEADER))) {
                $headers[self::AUTH_HEADER] = $headerToken;
            }
        }

        if ($this->hasToken()) {
            $headers[self::AUTH_HEADER] = 'Bearer ' . $this->json()->getToken();
        }

        // by default we want to set the status code to 400 if there are errors.
        // And only if it has not been specified before.

        if ($this->hasErrors() && $this->getStatusCode() == 200) {
            $this->json()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $response = response()
            ->json($this->responseArray(), $this->getStatusCode(), $headers);

        return $response;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 28/11/2017
 * Time: 10:16 PM
 */

namespace Kevupton\LaravelJsonResponse\Middleware;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Kevupton\LaravelJsonResponse\Exceptions\ExceptionHandler;
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
        $this->_response = $next($request);

        if ((new ExceptionHandler($this->_response->exception, $this))->failed()) {
            return $this->_response;
        }

        return $this->makeJsonResponse();
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
                // for each different type of content, we will do a different thing.
                // for models we want to add it to the snake_case model name on the data object
                if ($content instanceof Model) {
                    $this->json()->set(snake_case(last(explode("\\", get_class($content)))), $content);
                } elseif ($content instanceof Arrayable) {
                    $this->json()->merge($content->toArray());
                } elseif (is_array($content)) {
                    $this->json()->merge($content);
                } else {
                    $this->json()->add($content);
                }
            }

            if ($_response->getStatusCode() !== 200) {
                $this->json()->setStatusCode($_response->getStatusCode());
            }
        }

        if ($_response->headers->has(self::AUTH_HEADER) &&
            ($headerToken = $_response->headers->get(self::AUTH_HEADER))) {
            $headers[self::AUTH_HEADER] = $headerToken;
            $this->json()->setToken(null);
        }
        elseif ($this->hasToken()) {
            $headers[self::AUTH_HEADER] = 'Bearer ' . $this->json()->getToken();
            $this->json()->setToken(null);
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

    /**
     * Handles the json response error
     *
     * @param JsonResponseErrorException $e
     * @return bool
     */
    public function handleJsonResponseErrorException (JsonResponseErrorException $e)
    {
        $this->json()
            ->error($e->getKey(), $e->getValue())
            ->setStatusCode($e->getCode());

        return false;
    }
}
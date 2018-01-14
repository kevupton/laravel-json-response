<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 30/11/2017
 * Time: 11:41 PM
 */

namespace Kevupton\LaravelJsonResponse\Exceptions;

use Kevupton\LaravelJsonResponse\Middleware\OutputJsonResponse;
use Kevupton\LaravelJsonResponse\Traits\HasJson;
use ReflectionClass;

class ExceptionHandler
{
    use HasJson;

    /**
     * @var \Exception
     */
    private $exception;
    /**
     * @var OutputJsonResponse
     */
    private $middleware;

    /**
     * @var bool whether or not the handler failed
     */
    private $failed = false;

    /**
     * @var array a list of exception handlers
     */
    private $exceptionHandlers = [];

    /**
     * ExceptionHandler constructor.
     * @param \Exception $exception
     * @param OutputJsonResponse $middleware
     */
    public function __construct(\Exception $exception = null, OutputJsonResponse $middleware)
    {
        $this->exception = $exception;
        $this->middleware = $middleware;
        $this->exceptionHandlers = config(LARAVEL_JSON_RESPONSE_CONFIG . '.exceptions', []);

        if ($this->exception) {
            $this->failed = $this->handle();
        }
    }

    /**
     * Handles the exception returning whether or not the handler failed.
     */
    protected function handle()
    {
        $method = camel_case('handle_' . $this->getExceptionShortName());
        $exceptions = config(LARAVEL_JSON_RESPONSE_CONFIG . '.exceptions');

        if (method_exists($this->middleware, $method)) {

            return $this->middleware->$method($this->exception);

        } else {
            $ran = false;
            $result = false;

            foreach ($exceptions as $exception => $case) {

                if (!is_a($this->exception, $exception)) {
                    continue;
                }

                $ran = true;

                if (is_array($case) && isset($case['error'])) {

                    foreach ($case as $key => $value) {
                        if (!is_callable([$this->json(), $key])) {
                            continue;
                        }
                        call_user_func_array([$this->json(), $key], is_array($value) ? $value : [$value]);
                    }

                } elseif (is_array($case)) {

                    $this->json()->error(...$case);

                } elseif (is_callable($case) && $case($this->exception, $this->json())) {

                    $result = true;

                } else {

                    $this->json()->error($case);

                }

            }

            if ($ran) return $result;

        }

        if (method_exists($this->middleware, 'handleException')) {
            return $this->middleware->handleException($this->exception);
        }

        return true;
    }

    /**
     * Returns the short name of the exception class
     *
     * @return string
     */
    public function getExceptionShortName()
    {
        return (new ReflectionClass($this->exception))->getShortName();
    }

    /**
     * Gets the exception class
     *
     * @return string
     */
    public function getExceptionClass()
    {
        return get_class($this->exception);
    }

    /**
     * Returns whether or not the handler failed
     *
     * @return bool
     */
    public function failed()
    {
        return $this->failed;
    }
}
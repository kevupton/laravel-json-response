<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 28/11/2017
 * Time: 9:48 PM
 */
namespace Kevupton\LaravelJsonResponse\Exceptions;

use Illuminate\Http\Response;
use Throwable;

class JsonResponseErrorException extends \Exception
{
    /**
     * @var null
     */
    private $key;

    /**
     * @var null
     */
    private $value;

    public function __construct ($key = null, $value = null, $statusCode = Response::HTTP_BAD_REQUEST, Throwable $previous = null)
    {
        parent::__construct("Error Data:\n" . print_r($key, true), $statusCode, $previous);
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * @return null
     */
    public function getKey ()
    {
        return $this->key;
    }

    /**
     * Gets the status code from the exception code.
     *
     * @return mixed
     */
    public function getStatusCode ()
    {
        return $this->code;
    }
}
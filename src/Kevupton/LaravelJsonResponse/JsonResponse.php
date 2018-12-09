<?php

namespace Kevupton\LaravelJsonResponse;

class JsonResponse
{
    private $data = array();
    private $errors = array();
    private $token = null;
    private $statusCode = 200;

    /**
     * Sets an error on the errors object
     *
     * @param $key
     * @param null $value
     * @return JsonResponse
     */
    public function setError ($key, $value = null)
    {
        return $this->error($key, $value);
    }

    /**
     * Sets the error messages of the json request.
     *
     * @param $key
     * @param null $value
     * @return $this
     */
    public function error ($key, $value = null)
    {
        if (is_array($key)) {
            return $this->mergeErrors($key);
        }

        if (is_null($value)) {
            $this->errors[] = $key;
        } else {
            $this->errors[$key] = $value;
        }

        return $this;
    }

    /**
     * Merges the errors array with the json response errors
     *
     * @param array $errors
     * @return $this
     */
    public function mergeErrors (array $errors)
    {
        $this->errors = array_merge($this->errors, $errors);
        return $this;
    }

    /**
     * Concatenates the input array with the error array
     *
     * @param array $errors
     * @return $this
     */
    public function addErrors (array $errors)
    {
        $this->errors = $this->errors + $errors;
        return $this;
    }

    /**
     * Gets the data from the JsonResponse
     *
     * @return array
     */
    public function getErrors ()
    {
        return $this->errors;
    }

    /**
     * Gets the data on the JsonResponse
     *
     * @return array
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * Gets the status code of the request.
     *
     * @return int
     */
    public function getStatusCode ()
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code of the request.
     *
     * @param $code
     * @return $this
     */
    public function setStatusCode ($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Sets a data property on the data object
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set ($key, $value = null)
    {
        if (is_array($key)) {
            $this->merge($key);
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Appends an item to the list of data
     *
     * @param mixed $value
     * @return $this
     */
    public function add ($value)
    {
        $this->data[] = $value;

        return $this;
    }

    /**
     * Merges an array of data with the response data.
     *
     * @param array $data
     * @return $this
     */
    public function merge (array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Removes an error from the error list.
     *
     * @param $key
     * @return $this
     */
    public function deleteError ($key)
    {
        unset($this->errors[$key]);
        return $this;
    }

    /**
     * Removes some data from the json data.
     *
     * @param $key
     * @return $this
     */
    public function delete ($key)
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Returns the object as a JSON encoded string.
     *
     * @return string
     */
    public function __toString ()
    {
        return $this->toString();
    }

    /**
     * Returns the JSON encoded array of the object
     *
     * @return string
     */
    public function toString ()
    {
        return json_encode($this->toArray());
    }

    /**
     * Converts the object into the readable array.
     *
     * @return array
     */
    public function toArray ()
    {
        $data = [
            'data' => $this->data,
            'errors' => $this->errors,
            'success' => $this->isSuccess(),
            'status_code' => $this->statusCode
        ];

        if (!is_null($this->token)) {
            $data['token'] = $this->token;
        }

        return $data;
    }

    /**
     * Checks if the object is successful .
     *
     * @return bool
     */
    public function isSuccess ()
    {
        return count($this->errors) == 0 && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Checks if the response has errors.
     *
     * @return bool
     */
    public function hasErrors ()
    {
        return count($this->errors) > 0;
    }

    /**
     * Gets the response token
     *
     * @return null
     */
    public function getToken ()
    {
        return $this->token;
    }

    /**
     * Sets the token of the JSON request.
     *
     * @param $token
     * @return $this
     */
    public function setToken ($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasToken ()
    {
        return isset($this->token);
    }
}
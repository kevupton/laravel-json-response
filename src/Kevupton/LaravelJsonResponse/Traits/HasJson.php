<?php

namespace Kevupton\LaravelJsonResponse\Traits;

use Kevupton\LaravelJsonResponse\JsonResponse;

trait HasJson
{
    /** @var  JsonResponse */
    private $_json = null;

    public function hasErrors ()
    {
        return $this->json()->hasErrors();
    }

    /**
     * @return JsonResponse
     */
    public function json ()
    {
        return $this->_json ?: ($this->_json = json_response());
    }

    public function responseArray ()
    {
        return $this->json()->toArray();
    }

    public function getStatusCode ()
    {
        return $this->json()->getStatusCode();
    }

    public function isSuccess ()
    {
        return $this->json()->isSuccess();
    }

    public function hasToken () {
        return $this->json()->hasToken();
    }
}
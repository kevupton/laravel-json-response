<?php

if (!function_exists('json_response')) {
    /**
     * Gets the apps JsonResponse
     * @return \Kevupton\LaravelJsonResponse\JsonResponse
     */
    function json_response ()
    {
        return app('eth.json');
    }
}
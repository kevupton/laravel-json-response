<?php

define('LARAVEL_JSON_RESPONSE_CONFIG', 'json-response');
define('LARAVEL_JSON_RESPONSE_KEY', 'laravel-json-response');

if (!function_exists('json_response')) {
    /**
     * Gets the apps JsonResponse
     * @return \Kevupton\LaravelJsonResponse\JsonResponse
     */
    function json_response ()
    {
        return app(LARAVEL_JSON_RESPONSE_KEY);
    }
}
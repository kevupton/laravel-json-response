<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Kevupton\LaravelJsonResponse\JsonResponse;

return [
  'exceptions' => [
      /**
       * Show model not found when receiving this error
       */
      ModelNotFoundException::class => [
          'error' => 'Model not found',
          'setStatusCode' => Response::HTTP_NOT_FOUND
      ],

      /**
       * Add all the errors from the validation and continue
       */
      ValidationException::class =>  function (ValidationException $e, JsonResponse $json)
      {
          $json
              ->mergeErrors($e->errors())
              ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
      }
  ]
];
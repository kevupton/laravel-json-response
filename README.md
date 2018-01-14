# Laravel Json Response

## An [Ethereal](https://github.com/kevupton/ethereal/wiki) Package

Easy way to implement API formatted json responses.

#### Format:
```json
{
    "data": {...},
    "errors": [],
    "success": true,
    "status_code": 200,
    "token": null
}
```

## Setup

Install:
```bash
composer require kevupton/laravel-json-response
```

Add the service provider to your app config:
```php
\Kevupton\LaravelJsonResponse\Providers\LaravelJsonResponseProvider::class,
```

Add the middleware to your `app\Http\Kernel.php`

Either:

```php
// Formats all responses in json. Catches errors listed in config and JsonResponseErrorExceptions
Kevupton\LaravelJsonResponse\Middleware\OutputJsonResponse, 

// Extends the OutputJsonResponse to catch all errors, to keep the JSON output
Kevupton\LaravelJsonResponse\Middleware\CatchAllExceptions, 
```

### Config

Publish the config by using the command:
```bash
php artisan vendor:publish
```

## Examples

#### Example returning the data

Usage:
```php
Route::get('test', function () {
    return ['hello' => true];
});
```

Output:
```json
{
    "data": {
      "hello": true
    },
    "errors": [],
    "success": true,
    "status_code": 200
}
```

-----

#### Example manipulating the JSON directly
You can also set data and tokens directly from this method.

Usage:
```php
Route::get('test', function () {
    json_response()->error('This an example error message')
        ->setStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST);
});
```

Output:
```json
{
    "data": [],
    "errors": [
      "This an example error message"
    ],
    "success": false,
    "status_code": 400
}
```

-----

#### Example returning a model
Models are added onto the data using snake_case.

Usage:
```php
Route::get('test', function () {
    return \App\Models\TestModel::find(2);
});
```

Output:
```json
{
    "data": {
        "test_model": {
            "id": 2
        }
    },
    "success": false,
    "status_code": 400
}
```

----


#### Example returning an Arrayable
Arrayable objects have toArray methods, which are merged with the data.

Usage:
```php
Route::get('test', function () {
    return \App\Models\TestModel::paginate();
});
```

Output:
```json
{
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1
            },
            {
                "id": 2
            },
            ...
        ],
        "first_page_url": "http://url/api/test?page=1",
        "from": 1,
        "last_page": 3,
        "last_page_url": "http://url/api/test?page=3",
        "next_page_url": "http://url/api/test?page=2",
        "path": "http://url/api/test",
        "per_page": 10,
        "prev_page_url": null,
        "to": 10,
        "total": 24
    },
    "errors": [],
    "success": true,
    "status_code": 200
}
```

----

#### Example with validation errors

Usage:
```php
Route::get('test', function () {
    throw new \Illuminate\Validation\ValidationException(\Validator::make([], ['test' => 'required']));
});
```

Output:
```json
{
    "data": [],
    "errors": {
        "test": [
            "The test field is required."
        ]
    },
    "success": false,
    "status_code": 422
}
```

---

#### Example Exception
*NOTE: `APP_DEBUG=true` will display a stack trace*

Usage:
```php
Route::get('test', function () {
    throw new Exception('test');
});
```

Output:
```json
{
    "data": [],
    "errors": [
        "test message",
        {
            "file": "C:\\Users\\kevin\\Projects\\laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php",
            "line": 172,
            "function": "runCallable",
            "class": "Illuminate\\Routing\\Route",
            "type": "->",
            "args": []
        },
        {...},
        {...},
        {...},
        {...},
        ...
    ],
    "success": false,
    "status_code": 500
}
```


### Exception Handling

Exceptions can be caught by using the config file:

```php

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
        ModelNotFoundException::class => 'Model not found', // OR
        ModelNotFoundException::class => ['NOT_FOUND', 'Model not found'], // OR
        ModelNotFoundException::class => [
            'error' => 'Model not found', // these are functions on the JsonResponse, being dynamically invoked
            'setStatusCode' => Response::HTTP_NOT_FOUND
        ],

        /**
         * Add all the errors from the validation and continue
         */
        ValidationException::class => function (ValidationException $e, JsonResponse $json) {
            $json
                ->mergeErrors($e->errors())
                ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    ]
];
```

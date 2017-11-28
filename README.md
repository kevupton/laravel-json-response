# Laravel Json Response

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
// Formats all responses in json. Only catchs JsonResponseErrorException
Kevupton\LaravelJsonResponse\Middleware\OutputJsonResponse, 

// All of above, and catches validation exceptions
Kevupton\LaravelJsonResponse\Middleware\CatchValidationExceptions, 


// All of above and catches all exceptions.
Kevupton\LaravelJsonResponse\Middleware\CatchAllExceptions, 
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
    json_response()->error('This an example error message')->setStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST);
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

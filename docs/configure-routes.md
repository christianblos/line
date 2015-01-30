## Configure routes

A **Route** defines which **Controller** will be executed for a specific URL. To configure
routes, you have to give an array to the constructor of _\Line\Routing\Router_:

```php
$routes = [
    '/article/<id>' => [
        'name' => 'article',
        'controller' => 'ArticleController:showArticle',
    ],
    '/api/article' => [
        'controller' => [
            'GET' => 'ApiArticleController:getList',
            'POST' => 'ApiArticleController:createArticle',
        ],
    ],
];

$errorController = function (\Exception $ex, $statusCode) {
    // show error
};

$router = new \Line\Routing\Router($routes, $errorController);
```

The array key of _$routes_ must be the requested **url**. You can define placeholders for parameters here.
For example `/article/<id>` will match to the url path _/article/123_ or _/article/someId_.
The Controller will then receive the paramter _id_ with the value _123_ or _someId_.
You can also specify a regular expression like `<id:\d+>` to only allow numbers in this case.

The array value of _$routes_ must be an array. It must have at least the **controller** definition.
You can also define a **name** and **params**.

To let the application know which Controller will be used for the error page, you have to set the
error Controller separately. This Route will have the name "error". Other than the normal Controllers,
the error Controller will receive the _Exception_ as first and the _status code_ as second parameter.
If _$errorController_ is _null_, the _App_ object will handle the error itself.

### The controller definition

You have a lot of possibilities to define a Controller. The value can be...

... a **callable**:

- Anonymous function.
- Callable array (`['Class', 'method']` or `[$object, 'method']`).
- Object with the `__invoke()` method.

... a **string** like "Controller:method". In this case an object of the Controller class will
be created on the fly. Then the method of this object will be executed.

... a **string** like "Controller". This will only work if the Controller class is callable, means it
has the `__invoke()` method.

You can also restrict a Controller to a specific **HTTP Method**. In this case the _controller definition_
must be an array with the Method as key (like _GET_ or _POST_) and the actual _controller definition_ as value.


### The name definition

The name is optional. But if you set it, it will give you two advantages:

1. You can execute `$app->buildUrl('name')` to create an URL to a defined Route.
2. You can acces this name in the Controller via `$app->getRouteName()` to check in which context you are for example.

### The params definition

The params are optional. You can define params for a Route that will be passed to the Controller. In the
Controller you can access them in the same ways as the placeholder parameters in the URL definition.
See Example:

```php
$routes = [
    '/some-url' => [
        'params' => [
            'foo' => 'bar'
        ],
        'controller' => function ($foo) {
            // $foo = "bar"
        },
    ],
];
```

--------------------

[Back to overview](index.md)
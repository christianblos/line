## Define Controller inside your Dependency Injection Container

Since this framework is not tied to a specific _Dependency Injection Container_, you have to
**extend the App** class and implement this by yourself. But don't worry, this is very easy to accomplish.

You just have to overwrite the _getControllerObject()_ method. The _$className_ parameter is the
class name you defined in your [Route configuration](configure-routes.md). Your DIC should know this
name to return the Controller object.

```php
class MyApp extends \Line\App
{
    protected function getControllerObject($className)
    {
        $container = $this->getContainer();
        
        // this will look differently depending
        // on which container you use
        return $container[$className];
    }
}
```

Here is an example that will show you how this works:

```php
// To make it simple, we just define an array as DIC. Normally this is a bad idea :)
$container = [];
$container['SomeController'] = new SomeController();
$container['foo'] = new HelpController();
$container['ErrorController'] = new ErrorController();

// The Controller must be a string like <containerArrayKey>:<method>
$routes = [
    '/' => [
        'controller' => 'SomeController:actionIndex',
    ],
    '/help' => [
        'controller' => 'foo:actionHelp',
    ],
];
$router = new \Line\Routing\Router($routes, 'ErrorController:actionError');

// Use our extended App class
$app = new MyApp($router);
$app->setContainer($container);
$app->run();
```

Now if the request points to "/", the App looks for the key _"SomeController"_ in the DIC to
get the Controller object. Then the method _actionIndex_ will be called.

--------------------

[Back to overview](index.md)
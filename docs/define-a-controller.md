## Controller parameters

All params that you define in the [Route configuration](configure-routes.md) can be accessed by
a parameter with the same name in the Controller.

For example you have the following Route configuration:

```php
$routes = [
    '/shop/product/<id:\d+>-<name>' => [
        'controller' => 'ProductController:showProduct',
        'params' => [
            'foo' => 'bar'
        ]
    ]
];
```

You can access the params like this:

```php
class ProductController
{
    public function showProduct($id, $name, $foo)
    {
        // ...
    }
}
```

The _order_ of the parameters doesn't matter. Only the **variable name is important**.


## Inject App/Request object into your Controller

You can also force the framework to inject the current **App** or **Request** object into your Controller method:

```php
use Line\App;
use Line\Http\Request;

class ProductController
{
    public function showProduct(App $app, Request $request, $id, $name, $foo)
    {
        // ...
    }
}
```

The _order_ of the parameters or the _variable name_ doesn't matter here. But you **need the type hint** to
make the injection work. It will also work if you extend the _App_ or _Request_ class and use the type hinting
with the extended class.

## The Response

A Controller must return a _Response_ object or a string. In the Response object you can set the status code
and headers.

```php
use Line\Http\Response;

class SomeController
{
    public function firstAction()
    {
        $response = new Response();
        $response->content = '{"foo":"bar"}';
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function secondAction()
    {
        return 'Just display this string';
    }
}
```

--------------------

[Back to overview](index.md)
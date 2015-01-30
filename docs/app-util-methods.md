## App utility methods

The App object handles the main application workflow. But it also provides a few utility methods
that you can use in your Controller.

### abort()

This method will throw a _Line\Exception\HttpException_. Use this method to abort the
current action and show the error page.

```php
public function someAction(App $app) {
    $app->abort(404, 'Something was not found');
}
```

### buildUrl()

The buildUrl() method uses the Router to build an URL based on your [Route configuration](configure-routes.md).

```php
use Line\App;

$routes = [
    '/article/<id>' => [
        'name' => 'article',
        'controller' => function (App $app) {
            $url = $app->buildUrl('article', ['id' => 99]);
            // $url has now the value "/article/99".
        }
    ]
];
```

### getRouteName()

You can use this method to receive the current executed Route name. See Example:

```php
use Line\App;

$routes = [
    '/' => [
        'name' => 'index',
        'controller' => function (App $app) {
            $name = $app->getRouteName();
            // $name has now the value 'index'
        }
    ]
];
```

### json()

This is a shortcut to create a JSON response. You can also set the status code with the second parameter.

```php
public function someAction(App $app) {
    $data = [
        'errorcode' => 404,
        'errormessage' => 'Not found',
    ];
    return $app->json($data, 404);
}
```

### redirect()

Use this method to redirect to another page. You can also just throw a
_Line\Exception\RedirectException_ manually. 

```php
public function someAction(App $app) {
    $app->redirect('http://www.example.com');
}
```

### render()

This method renders a simple php view. The path to the view should be an absolute path.

```php
public function someAction(App $app) {
    $data = [
        'foo' => 'bar'
    ];
    
    // In the view you can access $foo with the value "bar".
    
    return $app->render('/path/to/views/view.php', $data);
}
```

--------------------

[Back to overview](index.md)
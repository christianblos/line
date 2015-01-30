## What is Line?

Line is a very lightweight PHP Micro-Framework. It has the following features:

- Very easy to use.
- You can extend and replace ALL components.
- Flexible Controller definition.
- It only does what it needs (If you need more, just extend it).

## Install via Composer

Run `composer require line/line`.

## Example Usage

```php
// match URLs to their Controller
$routes = [
    '/' => [
        'name' => 'index',
        'controller' => 'SomeController:index',
    ],
    '/article/<id>' => [
        'name' => 'article',
        'controller' => function ($id) {
            // show article
        },
    ],
];

// define Controller for error page (optional but recommended)
$errorController = 'ErrorController:showError';

// create App object
$router = new \Line\Routing\Router($routes, $errorController);
$app = new \Line\App($router);

// show backtrace on errors
$app->debug = true;

// run application
$app->run();
```

See [docs](docs/index.md) for more information.

## License

The MIT license.
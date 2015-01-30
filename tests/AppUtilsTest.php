<?php
namespace Line;

use Line\Routing\Router;

/**
 *
 */
class AppUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testGetRouteName()
    {
        $routes = [
            '/' => [
                'name'       => 'theName',
                'controller' => function (App $app) {
                    return $app->getRouteName();
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router);

        $this->expectOutputString('theName');
        $app->run();
    }

    /**
     *
     */
    public function testAbort()
    {
        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    $app->abort(404, 'The message', [], 123);
                },
            ],
        ];

        $router = new Router($routes, function (\Exception $ex, $statusCode) {
            return $statusCode . ',' . $ex->getMessage() . ',' . $ex->getCode();
        });
        $app = new App($router);

        $this->expectOutputString('404,The message,123');
        $app->run();
    }

    /**
     *
     */
    public function testJson()
    {
        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    return $app->json(['foo' => 'bar']);
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router);

        $this->expectOutputString('{"foo":"bar"}');
        $app->run();
    }

    /**
     *
     */
    public function testBuildUrl()
    {
        $request = $this->getMock('\Line\Http\Request');

        $request->expects($this->any())
            ->method('getUrl')
            ->willReturn('/');

        $request->expects($this->any())
            ->method('getScheme')
            ->willReturn('http');

        $request->expects($this->any())
            ->method('getHost')
            ->willReturn('example.com');

        $routes = [
            '/'          => [
                'controller' => function (App $app) {
                    return $app->buildUrl('test', ['id' => 123])
                    . ','
                    . $app->buildUrl('test', ['id' => 123], true);
                },
            ],
            '/test/<id>' => [
                'name'       => 'test',
                'controller' => function () {
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router, $request);

        $this->expectOutputString('/test/123,http://example.com/test/123');
        $app->run();
    }

    /**
     *
     */
    public function testRender()
    {
        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    return $app->render(__DIR__ . '/fixtures/view.php', ['name' => 'Christian']);
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router);

        $this->expectOutputString('Hello Christian');
        $app->run();
    }

    /**
     *
     */
    public function testRenderNotFound()
    {
        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    return $app->render(__DIR__ . '/fixtures/invalid.php');
                },
            ],
        ];

        $router = new Router($routes, function (\Exception $ex, $code) {
            return $code;
        });
        $app = new App($router);

        $this->expectOutputString('500');
        $app->run();
    }

    /**
     *
     */
    public function testRenderWithException()
    {
        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    return $app->render(__DIR__ . '/fixtures/view.php', ['throw' => true]);
                },
            ],
        ];

        $router = new Router($routes, function (\Exception $ex, $code) {
            return $code . ' ' . $ex->getMessage();
        });
        $app = new App($router);

        $this->expectOutputString('500 from view');
        $app->run();
    }

    /**
     *
     */
    public function testRedirect()
    {
        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    $app->redirect('/redirect');
                    echo 'do not display';
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router);

        $this->expectOutputString('');
        $app->run();
    }
}

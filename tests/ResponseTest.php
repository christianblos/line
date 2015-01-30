<?php
namespace Line;

use Line\Http\Response;
use Line\Routing\Router;

/**
 *
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testResponse()
    {
        $routes = [
            '/' => [
                'controller' => function () {
                    $response = new Response('foo');
                    return $response;
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router);

        $this->expectOutputString('foo');
        $app->run();
    }
}

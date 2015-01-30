<?php
namespace Line;

use Line\Routing\Router;

/**
 *
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testContainer()
    {
        $container = [
            'someService' => 'content',
        ];

        $routes = [
            '/' => [
                'controller' => function (App $app) {
                    return $app->getContainer()['someService'];
                },
            ],
        ];

        $router = new Router($routes);
        $app = new App($router);
        $app->setContainer($container);

        $this->expectOutputString('content');
        $app->run();
    }
}

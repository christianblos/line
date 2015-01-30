<?php
namespace Line;

use Line\Routing\Router;

require_once __DIR__ . '/fixtures/SampleController.php';
require_once __DIR__ . '/fixtures/EmptyController.php';

/**
 *
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    private $errorController;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     *
     */
    protected function setUp()
    {
        $this->errorController = function (\Exception $ex, $code) {
            return $code;
        };

        $this->request = $this->getMock('\Line\Http\Request');
    }

    /**
     *
     */
    public function routesDataProvider()
    {
        return [
            ['/invalid', '404'],
            ['/', 'this is the index'],
            ['/anonymous', 'anonymous function'],
            ['/string', 'response'],
            ['/stringInvoke', 'invoke'],
            ['/stringInvalid', '500'],
            ['/stringNotCallable', '404'],
            ['/objectInvoke', 'invoke'],
            ['/arrayCallable', 'response'],
            ['/arrayEmpty', '404'],
            ['/httpMethod', 'response'],
            ['/exception', '500'],
        ];
    }

    /**
     * @dataProvider routesDataProvider
     */
    public function testController($url, $output)
    {
        $controller = new \SampleController();

        $this->request->expects($this->any())
            ->method('getUrl')
            ->willReturn($url);

        $this->request->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');

        $routes = [
            '/'                  => [
                'controller' => function () {
                    return 'this is the index';
                },
            ],
            '/anonymous'         => [
                'controller' => function () {
                    return 'anonymous function';
                },
            ],
            '/string'            => [
                'controller' => 'SampleController:actionReturnsString'
            ],
            '/stringInvoke'      => [
                'controller' => 'SampleController'
            ],
            '/stringInvalid'     => [
                'controller' => 'InvalidController'
            ],
            '/stringNotCallable' => [
                'controller' => 'EmptyController'
            ],
            '/objectInvoke'      => [
                'controller' => $controller
            ],
            '/arrayCallable'     => [
                'controller' => [$controller, 'actionReturnsString']
            ],
            '/arrayEmpty'        => [
                'controller' => []
            ],
            '/httpMethod'        => [
                'controller' => [
                    'GET' => 'SampleController:actionReturnsString'
                ],
            ],
            '/exception' => [
                'controller' => function() {
                    throw new \Exception();
                }
            ],
        ];

        $router = new Router($routes, $this->errorController);
        $app = new App($router, $this->request);

        $this->expectOutputString($output);
        $app->run();
    }

    /**
     *
     */
    public function testDefaultErrorControllerWithoutDebug()
    {
        $router = new Router([]);
        $app = new App($router);

        $this->expectOutputString('Error 404');
        $app->run();
    }

    /**
     *
     */
    public function testDefaultErrorControllerWithDebug()
    {
        $router = new Router([]);
        $app = new App($router);
        $app->debug = true;

        $this->expectOutputRegex('/^Error 404.*\#[0-9]/');
        $app->run();
    }

    /**
     *
     */
    public function testErrorControllerThrowsException()
    {
        $errorController = function () {
            throw new \Exception();
        };

        $router = new Router([], $errorController);
        $app = new App($router);

        $this->expectOutputString('Error 500');
        $app->run();
    }

    /**
     * @depends testErrorControllerThrowsException
     */
    public function testDeug()
    {
        $errorController = function () {
            throw new \Exception();
        };

        $router = new Router([], $errorController);
        $app = new App($router);
        $app->debug = true;

        $this->expectOutputRegex('/^Error 500.*\#[0-9]/');
        $app->run();
    }
}

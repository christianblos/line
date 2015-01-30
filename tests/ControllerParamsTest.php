<?php
namespace Line;

use Line\Http\Request;
use Line\Routing\Router;

/**
 *
 */
class ControllerParamsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     *
     */
    protected function setUp()
    {
        $this->request = $this->getMock('\Line\Http\Request');
    }

    /**
     *
     */
    public function urlParamsDataProvider()
    {
        return [
            ['/test-123', '123'],
            ['/test-a', 'a'],
            ['/test-', '__error__'],
            ['/help/test', 'test'],
            ['/help/foo', 'foo'],
            ['/help/1', '__error__'],
            ['/unused', '2'],
        ];
    }

    /**
     * @dataProvider urlParamsDataProvider
     */
    public function testUrlParams($url, $response)
    {
        $this->request->expects($this->any())
            ->method('getUrl')
            ->willReturn($url);

        $routes = [
            '/test-<id>' => [
                'controller' => function ($id) {
                    return $id;
                }
            ],
            '/help/<name:[a-z]+>' => [
                'controller' => function ($name) {
                    return $name;
                }
            ],
            '/unused' => [
                'controller' => function ($one, $two = 2) {
                    return $one . $two;
                }
            ],
        ];

        $errorController = function () {
            return '__error__';
        };

        $router = new Router($routes, $errorController);
        $app = new App($router, $this->request);

        $this->expectOutputString($response);
        $app->run();
    }

    /**
     *
     */
    public function injectDataProvider()
    {
        return [
            ['/first'],
            ['/second'],
        ];
    }

    /**
     * @dataProvider injectDataProvider
     */
    public function testInject($url)
    {
        $this->request->expects($this->any())
            ->method('getUrl')
            ->willReturn($url);

        $self = $this;

        $routes = [
            '/first' => [
                'controller' => function (App $app, Request $req) use ($self) {
                    $self->assertInstanceOf('Line\App', $app);
                    $self->assertInstanceOf('Line\Http\Request', $req);
                    return $req->getUrl();
                }
            ],
            '/second' => [
                'controller' => function ($dummy, Request $request, App $application) use ($self) {
                    $self->assertNull($dummy);
                    $self->assertInstanceOf('Line\Http\Request', $request);
                    $self->assertInstanceOf('Line\App', $application);
                    return $request->getUrl();
                }
            ],
        ];

        $router = new Router($routes);
        $app = new App($router, $this->request);

        $this->expectOutputString($url);
        $app->run();
    }

    /**
     *
     */
    public function testRouteParams()
    {
        $this->request->expects($this->any())
            ->method('getUrl')
            ->willReturn('/some-99');

        $self = $this;

        $routes = [
            '/some-<id>' => [
                'controller' => function ($foo, $id, $num) use ($self) {
                    $self->assertEquals('bar', $foo);
                    $self->assertEquals('99', $id);
                    $self->assertEquals(1, $num);
                },
                'params' => [
                    'foo' => 'bar',
                    'num' => 1,
                ]
            ],
        ];

        $router = new Router($routes);
        $app = new App($router, $this->request);

        $this->expectOutputString('');
        $app->run();
    }
}

<?php
namespace Line;

use Line\Exception\HttpException;
use Line\Exception\RedirectException;
use Line\Http\RedirectResponse;
use Line\Http\Request;
use Line\Http\Response;
use Line\Routing\Route;
use Line\Routing\RouterInterface;

/**
 * Handles application flow.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class App
{

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var mixed
     */
    protected $container;

    /**
     * @var Route
     */
    protected $currentRoute;

    /**
     * @param RouterInterface $router
     * @param Request         $request
     */
    public function __construct(RouterInterface $router, Request $request = null)
    {
        $this->router = $router;
        $this->request = $request !== null ? $request : new Request();
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            $url = $this->request->getUrl(true);

            $route = $this->router->parseUrl($url);
            $response = $this->executeRoute($route);

            if ($response instanceof Response) {
                $response->flush();
            } elseif (is_scalar($response)) {
                echo $response;
            }
        } catch(\Exception $ex) {
            $this->handleException($ex, 500);
        }
    }

    /**
     * Get controller object by class name.
     *
     * @param string $className
     *
     * @return mixed
     */
    protected function getControllerObject($className)
    {
        if (!class_exists($className)) {
            throw new \Exception('Controller class "' . $className . '" does not exist');
        }

        return new $className();
    }

    /**
     * @param string $controller
     *
     * @return mixed
     */
    protected function getControllerCallable($controller)
    {
        if (is_callable($controller)) {
            return $controller;
        }

        if (is_string($controller)) {

            // allow definitions like "SomeClass:someMethod"
            if (stristr($controller, ':')) {
                list($class, $method) = explode(':', $controller, 2);
                $obj = $this->getControllerObject($class);
                return [$obj, $method];
            }

            // allow defining classes with the invoke method
            $obj = $this->getControllerObject($controller);
            if (is_callable($obj)) {
                return $obj;
            }
        }

        if (is_array($controller)) {
            $httpMethod = $this->request->getMethod();
            if (isset($controller[$httpMethod])) {
                return $this->getControllerCallable($controller[$httpMethod]);
            }
        }

        return null;
    }

    /**
     * @param array $routeParams
     * @param mixed $controller
     *
     * @return array
     */
    protected function filterParamsForController($routeParams, $controller)
    {
        if (is_array($controller)) {
            $ref = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof \Closure) {
            $ref = new \ReflectionObject($controller);
            $ref = $ref->getMethod('__invoke');
        } else {
            $ref = new \ReflectionFunction($controller);
        }

        $appClass = __CLASS__;
        $requestClass = 'Line\Http\Request';

        $params = [];
        foreach ($ref->getParameters() as $refParam) {
            $cls = $refParam->getClass();
            if ($cls && ($cls->name == $appClass || $cls->isSubclassOf($appClass))) {
                $params[] = $this;
            } elseif ($cls && ($cls->name == $requestClass || $cls->isSubclassOf($requestClass))) {
                $params[] = $this->request;
            } elseif (isset($routeParams[$refParam->name])) {
                $params[] = $routeParams[$refParam->name];
            } elseif ($refParam->isDefaultValueAvailable()) {
                $params[] = $refParam->getDefaultValue();
            } else {
                $params[] = null;
            }
        }

        return $params;
    }

    /**
     * @param Route|null $route
     * @param array|null $forceParams
     * @param bool       $throwError
     *
     * @return mixed
     */
    protected function executeRoute(Route $route = null, $forceParams = null, $throwError = false)
    {
        try {
            if (!$route) {
                throw new HttpException(404, 'no route found for current url');
            }

            $this->currentRoute = $route;

            $callable = $this->getControllerCallable($route->controller);
            if (!$callable) {
                throw new HttpException(404, 'no controller found for route "' . $route->name . '"');
            }

            $params = $this->filterParamsForController($route->params, $callable);
            if ($forceParams !== null) {
                foreach ($forceParams as $key => $value) {
                    $params[$key] = $value;
                }
            }

            $response = call_user_func_array($callable, $params);
        } catch(RedirectException $ex) {
            $response = new RedirectResponse($ex->getUrl(), $ex->getStatusCode());
        } catch(HttpException $ex) {
            $params = [$ex, $ex->getStatusCode()];
            $response = $this->executeRoute($this->getErrorRoute(), $params);

            if (!$response instanceof Response) {
                $response = new Response($response);
            }

            foreach ($ex->getHeaders() as $name => $value) {
                $response->headers->set($name, $value);
            }
        } catch(\Exception $ex) {
            if ($throwError) {
                throw $ex;
            }
            $params = [$ex, 500];
            $response = $this->executeRoute($this->getErrorRoute(), $params, true);
        }

        return $response;
    }

    /**
     * @return Route
     */
    protected function getErrorRoute()
    {
        $route = $this->router->getErrorRoute();
        if ($route->controller === null) {
            $route->controller = [$this, 'handleException'];
        }
        return $route;
    }

    /**
     * Handle all exceptions.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return void
     */
    protected function handleException(\Exception $exception, $code)
    {
        if (!headers_sent()) {
            header('HTTP/1.0 500');
        }
        echo 'Error ' . $code;

        if ($this->debug) {
            echo '<br/><br/>';
            echo $exception->getMessage();
            echo '<br/><br/>';
            echo nl2br($exception->getTraceAsString());
        }
    }

    /**
     * Get current executing route name.
     *
     * @return string|null
     */
    public function getRouteName()
    {
        return $this->currentRoute ? $this->currentRoute->name : null;
    }

    /**
     * Abort current request.
     *
     * @param int    $statusCode
     * @param string $message
     * @param array  $headers
     * @param int    $errorCode
     *
     * @return void
     */
    public function abort($statusCode, $message = null, array $headers = [], $errorCode = 0)
    {
        throw new HttpException($statusCode, $message, $headers, $errorCode);
    }

    /**
     * Redirect to another URL.
     *
     * @param string $url
     * @param int    $statusCode
     *
     * @return void
     */
    public function redirect($url, $statusCode = 302)
    {
        throw new RedirectException($url, $statusCode);
    }

    /**
     * Get JSON response.
     *
     * @param mixed $data
     * @param int   $statusCode
     *
     * @return Response
     */
    public function json($data = [], $statusCode = 200)
    {
        $response = new Response();
        $response->statusCode = $statusCode;
        $response->content = json_encode($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Build url by route name.
     *
     * @param string $routeName
     * @param array  $params
     * @param bool   $absolute
     *
     * @return string
     */
    public function buildUrl($routeName, array $params = [], $absolute = false)
    {
        $url = $this->router->buildUrl($routeName, $params);
        if ($absolute) {
            $url = $this->request->getScheme() . '://' . $this->request->getHost() . $url;
        }
        return $url;
    }

    /**
     * Render php view and return its content.
     *
     * @param string $file
     * @param array  $data
     *
     * @return string
     */
    public function render($file, array $data = [])
    {
        if (!file_exists($file)) {
            throw new \Exception('view "' . $file . '" doesn\'t exist');
        }

        foreach ($data as $name => $value) {
            $$name = $value;
        }

        ob_start();
        try {
            include $file;
            return ob_get_clean();
        } catch (\Exception $ex) {
            ob_end_clean();
            throw $ex;
        }
    }
}

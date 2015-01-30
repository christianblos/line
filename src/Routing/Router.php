<?php
namespace Line\Routing;

/**
 * Match URL to its controller.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class Router implements RouterInterface
{

    /**
     * @var array
     */
    private $config;

    /**
     * @var Route
     */
    private $errorRoute;

    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * @param array  $config
     * @param mixed  $errorController
     * @param string $baseUrl         The base path of the url (without a trailing slash!).
     */
    public function __construct(array $config, $errorController = null, $baseUrl = null)
    {
        $this->config = $config;
        $this->baseUrl = $baseUrl;

        $this->errorRoute = new Route();
        $this->errorRoute->name = 'error';
        $this->errorRoute->controller = $errorController;
    }

    /**
     * Parse URL and return its controller.
     *
     * @param string $url
     *
     * @return Route|null
     */
    public function parseUrl($url)
    {
        $urlPath = parse_url($url, PHP_URL_PATH);
        if ($urlPath && $urlPath != '/') {
            $urlPath = rtrim($urlPath, '/');
        } else {
            $urlPath = '/';
        }

        foreach ($this->config as $routePattern => $config) {
            // build regex to match url
            $split = preg_split('/(<\w+(?::.*|)>)/U', $routePattern, -1, PREG_SPLIT_DELIM_CAPTURE);
            $regex = $this->baseUrl ? preg_quote($this->baseUrl, '/') : '';
            $names = [];
            foreach ($split as $part) {
                if ($part) {
                    if (preg_match('/<(\w+)(?::(.*)|)>/', $part, $matches)) {
                        $name = $matches[1];
                        if (isset($matches[2])) {
                            $pattern = str_replace('(', '(?:', $matches[2]);
                        } else {
                            $pattern = '[^\/]+';
                        }
                        $names[] = $name;
                        $regex .= '(' . $pattern . ')';
                    } else {
                        $regex .= preg_quote($part, '/');
                    }
                }
            }

            // check url and match values
            if (preg_match('/^' . $regex . '\/?$/', $urlPath, $matches)) {
                $route = $this->getRouteByConfig($routePattern, $config);

                foreach ($names as $idx => $name) {
                    $route->params[$name] = $matches[$idx + 1];
                }

                return $route;
            }
        }

        return null;
    }

    /**
     * Build url by route name.
     *
     * @param string $routeName
     * @param array  $params
     *
     * @return string|null
     */
    public function buildUrl($routeName, array $params = [])
    {
        $url = null;

        foreach ($this->config as $routePattern => $config) {
            $route = $this->getRouteByConfig($routePattern, $config);

            if ($routeName == $route->name) {
                foreach ($params as $key => $value) {
                    if (isset($route->params[$key]) && $route->params[$key] != $value) {
                        continue 2;
                    }
                }
                if (preg_match_all('/<(\w+)(?::(.*)|)>/U', $routePattern, $matches)) {
                    $replaceValues = array();
                    foreach ($matches[1] as $idx => $key) {
                        if (isset($params[$key])) {
                            if ($matches[2][$idx] && !preg_match('/^' . $matches[2][$idx] . '$/', $params[$key])) {
                                continue 2;
                            }
                            $replaceValues[] = $params[$key];
                        } else {
                            continue 2;
                        }
                    }
                    $url = $this->baseUrl . str_replace($matches[0], $replaceValues, $routePattern);
                } else {
                    $url = $this->baseUrl . $routePattern;
                }
                break;
            }
        }

        if ($url && $url != '/') {
            $url = preg_replace('/\/+/', '/', $url);
            $url = rtrim($url, '/');
        }
        return $url;
    }

    /**
     * Get route for error page.
     *
     * @return Route
     */
    public function getErrorRoute()
    {
        return $this->errorRoute;
    }

    /**
     * Get route object by config.
     *
     * @param string $routePattern
     * @param array  $config
     *
     * @return Route
     */
    private function getRouteByConfig($routePattern, $config)
    {
        if (!is_array($config)) {
            throw new \Exception('invalid route config for route "' . $routePattern . '"');
        }
        if (!isset($config['controller'])) {
            throw new \Exception('no controller defined in url config for route "' . $routePattern . '"');
        }

        $route = new Route();
        $route->controller = $config['controller'];

        if (isset($config['name'])) {
            $route->name = $config['name'];
        }

        if (isset($config['params'])) {
            $route->params = $config['params'];
        }

        return $route;
    }
}
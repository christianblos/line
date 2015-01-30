<?php
namespace Line\Routing;

/**
 * Structure for url router.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
interface RouterInterface
{

    /**
     * Parse URL and return its controller.
     *
     * @param string $url
     *
     * @return Route|null
     */
    public function parseUrl($url);

    /**
     * Build url by route name.
     *
     * @param string $routeName
     * @param array  $params
     *
     * @return string
     */
    public function buildUrl($routeName, array $params = []);

    /**
     * Get route for error page.
     *
     * @return Route
     */
    public function getErrorRoute();
}

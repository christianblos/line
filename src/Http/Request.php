<?php
namespace Line\Http;

/**
 * Holds requested data.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class Request
{

    /**
     * @var Params
     */
    public $query;

    /**
     * @var Params
     */
    public $post;

    /**
     * @var Params
     */
    public $cookies;

    /**
     * @var Params
     */
    public $server;

    /**
     * @var Params
     */
    public $files;

    /**
     * @param array|null $query
     * @param array|null $post
     * @param array|null $cookies
     * @param array|null $server
     * @param array|null $files
     */
    public function __construct($query = null, $post = null, $cookies = null, $server = null, $files = null)
    {
        $this->query = new Params($query !== null ? $query : $_GET);
        $this->post = new Params($post !== null ? $post : $_POST);
        $this->cookies = new Params($cookies !== null ? $cookies : $_COOKIE);
        $this->server = new Params($server !== null ? $server : $_SERVER);
        $this->files = new Params($files !== null ? $files : $_FILES);
    }

    /**
     * Get raw POST data.
     *
     * @return string
     */
    public function getRawPost()
    {
        return file_get_contents('php://input');
    }

    /**
     * Get hostname.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->server->get('HTTP_HOST');
    }

    /**
     * Check if url is called via HTTPS.
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->server->get('HTTPS') === 'on';
    }

    /**
     * Geth requested URL scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * Get requested url.
     *
     * @param bool $full
     *
     * @return string
     */
    public function getUrl($full = false)
    {
        $url = $this->server->get('REQUEST_URI');
        if ($full) {
            $url = $this->getScheme() . '://' . $this->getHost() . $url;
        }
        return $url;
    }

    /**
     * Get referer url.
     *
     * @return string
     */
    public function getReferer()
    {
        return $this->server->get('HTTP_REFERER');
    }

    /**
     * Get a header value.
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeader($name)
    {
        return $this->server->get('HTTP_' . strtoupper(str_replace('-', '_', $name)));
    }

    /**
     * Get requested method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->server->get('REQUEST_METHOD');
    }

    /**
     * Check if request is a GET request.
     *
     * @return boolean
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Check if request is a POST request.
     *
     * @return boolean
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Check if request is an ajax request.
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return $this->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }
}

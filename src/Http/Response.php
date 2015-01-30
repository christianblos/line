<?php
namespace Line\Http;

/**
 * Handles http response.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class Response
{

    /**
     * @var int
     */
    public $statusCode = 200;

    /**
     * @var Params
     */
    public $headers;

    /**
     * @var string
     */
    public $content = '';

    /**
     * @param string $content
     */
    public function __construct($content = null)
    {
        $this->content = $content;
        $this->headers = new Params([]);
    }

    /**
     * @return void
     */
    public function flush()
    {
        if (!headers_sent()) {
            header('HTTP/1.1 ' . $this->statusCode);
            foreach ($this->headers->all() as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->content;
    }
}
<?php
namespace Line\Exception;

/**
 * Exception that redirects to another url.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class RedirectException extends \Exception
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $statusCode = 0;

    /**
     * @param string $url
     * @param int    $statusCode
     */
    public function __construct($url, $statusCode = 302)
    {
        $this->url = $url;
        $this->statusCode = $statusCode;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
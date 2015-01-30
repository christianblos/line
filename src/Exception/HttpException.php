<?php
namespace Line\Exception;

/**
 * Exception that shows the error page with a specific status code.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class HttpException extends \Exception
{

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param int    $statusCode
     * @param string $message
     * @param array  $headers
     * @param int    $errorCode
     */
    public function __construct($statusCode, $message = null, array $headers = [], $errorCode = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        parent::__construct($message, $errorCode);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}

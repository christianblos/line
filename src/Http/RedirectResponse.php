<?php
namespace Line\Http;

/**
 * Redirects to another url.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class RedirectResponse extends Response
{

    /**
     * @var string
     */
    public $url;

    /**
     * @param string $url
     * @param int    $statusCode
     */
    public function __construct($url, $statusCode = 302)
    {
        $this->url = $url;
        $this->statusCode = $statusCode;
        parent::__construct();
        $this->headers->set('Location', $this->url);
    }
}

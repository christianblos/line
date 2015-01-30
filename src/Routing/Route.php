<?php
namespace Line\Routing;

/**
 * A route.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class Route
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $controller;

    /**
     * @var array
     */
    public $params = [];
}

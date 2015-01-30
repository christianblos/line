<?php
namespace Line\Http;

/**
 * Container for parameters.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/christianblos
 */
class Params
{

    /**
     * @var array
     */
    private $params = [];

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function get($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
        return null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function remove($name)
    {
        unset($this->params[$name]);
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * Get all param keys.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->params);
    }
}

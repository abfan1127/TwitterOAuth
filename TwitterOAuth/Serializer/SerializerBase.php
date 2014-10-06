<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth\Serializer;

class SerializerBase
{
    protected $config = array();


    public function __construct(array $config = array())
    {
        $this->config = $config;

        unset($config);
    }

    /**
     * Get Config values
     *
     * @param null $key
     * @return array|string|false
     */
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return false;
    }
}

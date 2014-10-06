<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth;

use TwitterOAuth\Serializer\SerializerInterface;

interface OAuthInterface
{
    public function __construct(array $config, SerializerInterface $serializer = null);

    public function get($call, array $getParams = null);

    public function post($call, array $postParams = null, array $getParams = null);
}

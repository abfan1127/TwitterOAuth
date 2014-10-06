<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 *
 * Application-only authentication
 * https://dev.twitter.com/oauth/application-only
 */

namespace TwitterOAuth;

class ApplicationOnly extends OAuthBase implements OAuthInterface
{
    protected $url = array(
        'domain' => 'https://api.twitter.com',
        'api' => '/1.1/',
        'getToken' => '/oauth2/token',
        'invToken' => '/oauth2/invalidate_token',
    );

    protected $bearerToken = null;


    /**
     *  Set a bearer token
     *
     * @param string $bearerToken  Bearer Token
     * @return bool
     */
    public function setBearerToken($bearerToken)
    {
        $this->bearerToken = $bearerToken;

        unset($bearerToken);

        return true;
    }

    /**
     *  Get a bearer token from consumer keys
     *
     * @return null|string  Bearer token
     * @throws Exception\CurlException
     * @throws Exception\TwitterException
     */
    public function getBearerToken()
    {
        $url = $this->getBearerTokenUrl();

        $params = array(
            'post' => array('grant_type' => 'client_credentials'),
            'headers' => $this->buildBearerTokenHeader(),
        );

        $response = $this->curl->send($url, $params);

        $obj = json_decode($response['body']);

        if (!$obj || !isset($obj->token_type) || $obj->token_type != 'bearer') {
            $this->findExceptions($response);
        }

        unset($url, $params, $response);

        $this->bearerToken = rawurldecode($obj->access_token);

        return $this->bearerToken;
    }

    /**
     *  Invalidate a bearer token
     *
     * @return bool
     * @throws Exception\CurlException
     * @throws Exception\TwitterException
     */
    public function invalidateBearerToken()
    {
        $url = $this->getInvalidateBearerTokenUrl();

        $bearerToken = $this->bearerToken;

        if ($bearerToken === null) {
            $bearerToken = $this->getBearerToken();
        }

        $params = array(
            'post' => array('access_token' => $bearerToken),
            'headers' => $this->buildBearerTokenHeader(),
        );

        $response = $this->curl->send($url, $params);

        $obj = json_decode($response['body']);

        if (!$obj || !isset($obj->access_token) || $obj->access_token != $bearerToken) {
            $this->findExceptions($response);
        }

        unset($url, $bearerToken, $params, $obj, $response);

        return true;
    }


    /**
     * Getting Twitter API URL
     *
     * @return string  API URL
     */
    protected function getUrl()
    {
        return $this->url['domain'] . $this->url['api'] . $this->call . '.json';
    }

    /**
     * Getting bearer token URL
     *
     * @return string  Bearer token URL
     */
    protected function getBearerTokenUrl()
    {
        return $this->url['domain'] . $this->url['getToken'];
    }

    /**
     * Getting invalidate bearer token URL
     *
     * @return string  Invalidate bearer token URL
     */
    protected function getInvalidateBearerTokenUrl()
    {
        return $this->url['domain'] . $this->url['invToken'];
    }

    /**
     * Generate bearer token credentials
     *
     * @return string  Bearer token credentials
     */
    protected function getBearerTokenCredentials()
    {
        $signingKey = rawurlencode($this->getConfig('consumer_key')) . ':' . rawurlencode($this->getConfig('consumer_secret'));

        return base64_encode($signingKey);
    }

    /**
     * Building request bearer token HTTP headers
     *
     * @return array  HTTP headers
     */
    protected function buildBearerTokenHeader()
    {
        return array(
            'Authorization: Basic ' . $this->getBearerTokenCredentials(),
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Expect:'
        );
    }

    /**
     * Building request HTTP headers
     *
     * @return array  HTTP headers
     */
    protected function buildRequestHeader()
    {
        $bearerToken = $this->bearerToken;

        if ($this->bearerToken === null) {
            $bearerToken = $this->getBearerToken();
        }

        return array(
            'Authorization: Bearer ' . rawurlencode($bearerToken),
            'Expect:'
        );
    }
}

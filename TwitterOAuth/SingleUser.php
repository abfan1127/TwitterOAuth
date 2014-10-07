<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 *
 * Single-user OAuth with Examples
 * https://dev.twitter.com/oauth/overview/single-user
 *
 * Tokens from dev.twitter.com
 * https://dev.twitter.com/oauth/overview/application-owner-access-tokens
 */

namespace TwitterOAuth;

class SingleUser extends OAuthBase implements OAuthInterface
{
    protected $url = array(
        'domain' => 'https://api.twitter.com/1.1/',
        'upload' => 'https://upload.twitter.com/1.1/',
    );


    /**
     * Send a POST call with media upload to Twitter API via OAuth
     *
     * @param string $call  Twitter resource string
     * @param string $filename  File location to upload
     * @return mixed  Output with selected format
     * @throws Exception\CurlException
     * @throws Exception\TwitterException
     */
    public function postMedia($call, $filename)
    {
        $this->method = 'POST';

        $this->call = $call;

        $mimeBoundary = sha1($call . microtime());

        $params = array(
            'post' => $this->buildMultipart($mimeBoundary, $filename),
            'headers' => $this->buildUploadMediaHeader($mimeBoundary),
        );

        $response = $this->curl->send($this->getUrl(), $params);

        $obj = json_decode($response['body']);

        if (!$obj || !isset($obj->token_type) || $obj->token_type != 'bearer') {
            $this->findExceptions($response);
        }

        $this->headers = $response['headers'];

        unset($call, $filename, $mimeBoundary, $params, $obj);

        return $this->serializer->format($response['body']);
    }


    /**
     * Getting full URL from a Twitter resource
     *
     * @return string  Full URL
     */
    protected function getUrl()
    {
        $key = 'domain';

        $trace = end(debug_backtrace());

        if (!empty($trace) && !empty($trace['function']) && $trace['function'] == 'postMedia') {
            $key = 'upload';
        }

        unset($trace);

        return $this->url[$key] . $this->call . '.json';
    }

    /**
     * Getting OAuth parameters to be used in request headers
     *
     * @return array  OAuth parameters
     */
    protected function getOauthParameters()
    {
        $time = time();

        return array(
            'oauth_consumer_key' => $this->getConfig('consumer_key'),
            'oauth_nonce' => trim(base64_encode($time), '='),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $time,
            'oauth_token' => $this->getConfig('oauth_token'),
            'oauth_version' => '1.0'
        );
    }

    /**
     * Converting all parameters agetrrays to a single string with encoded values
     *
     * @return string  Single string with encoded values
     */
    protected function getRequestString()
    {
        $params = array_merge($this->getParams, $this->postParams, $this->getOauthParameters());

        $params = $this->curl->getParams($params);

        return rawurlencode($params);
    }

    /**
     * Getting OAuth signature base string
     *
     * @return string  OAuth signature base string
     */
    protected function getSignatureBaseString()
    {
        $method = strtoupper($this->method);

        $url = rawurlencode($this->getUrl());

        return $method . '&' . $url . '&' . $this->getRequestString();
    }

    /**
     * Getting a signing key
     *
     * @return string  Signing key
     */
    protected function getSigningKey()
    {
        return $this->getConfig('consumer_secret') . '&' . $this->getConfig('oauth_token_secret');
    }

    /**
     * Calculating the signature
     *
     * @return string  Signature
     */
    protected function calculateSignature()
    {
        return base64_encode(hash_hmac('sha1', $this->getSignatureBaseString(), $this->getSigningKey(), true));
    }

    /**
     * Converting OAuth parameters array to a single string with encoded values
     *
     * @return string  Single string with encoded values
     */
    protected function getOauthString()
    {
        $oauth = array_merge($this->getOauthParameters(), array('oauth_signature' => $this->calculateSignature()));

        ksort($oauth);

        $values = array();

        foreach ($oauth as $key => $value) {
            $values[] = $key . '="' . rawurlencode($value) . '"';
        }

        $oauth = implode(', ', $values);

        unset($values, $key, $value);

        return $oauth;
    }

    /**
     * Building request HTTP headers
     *
     * @return array  HTTP headers
     */
    protected function buildRequestHeader()
    {
        return array(
            'Authorization: OAuth ' . $this->getOauthString(),
            'Expect:'
        );
    }

    /**
     * Building upload media headers
     *
     * @param string $mimeBoundary  MIME boundary ID
     * @return array  HTTP headers
     */
    protected function buildUploadMediaHeader($mimeBoundary)
    {
        return array(
            'Authorization: OAuth ' . $this->getOauthString(),
            'Content-Type: multipart/form-data; boundary=' . $mimeBoundary,
            'Expect:'
        );
    }
}

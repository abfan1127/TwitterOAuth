<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth;

use TwitterOAuth\Common\Curl;
use TwitterOAuth\Exception\FileNotFound;
use TwitterOAuth\Exception\FileNotReadable;
use TwitterOAuth\Exception\TwitterException;
use TwitterOAuth\Exception\UnsupportedMime;
use TwitterOAuth\Serializer\ObjectSerializer;
use TwitterOAuth\Serializer\SerializerInterface;

class OAuthBase
{
    const EOL = "\r\n";


    protected $config = array();

    protected $serializer = null;

    protected $curl = null;

    protected $headers = null;


    protected $call = null;

    protected $method = null;

    protected $getParams = array();

    protected $postParams = array();


    public function __construct(array $config, SerializerInterface $serializer = null)
    {
        $this->config = $config;

        $this->serializer = $serializer ?: new ObjectSerializer();

        $this->curl = new Curl();

        unset($config, $serializer);
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

    /**
     * Get response headers
     *
     * @param null $key
     * @return array|string|false
     */
    public function getHeaders($key = null)
    {
        if ($key === null) {
            return $this->headers;
        }

        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }

        return false;
    }

    /**
     * Send a GET call to Twitter API via OAuth
     *
     * @param string $call  Twitter resource string
     * @param array $getParams  GET parameters to send
     * @return mixed  Output with selected format
     * @throws TwitterException
     */
    public function get($call, array $getParams = null)
    {
        $this->method = 'GET';

        $this->call = $call;

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        $response = $this->getResponse();

        $this->findExceptions($response);

        $this->headers = $response['headers'];

        unset($call, $getParams);

        return $this->serializer->format($response['body']);
    }

    /**
     * Send a POST call to Twitter API via OAuth
     *
     * @param string $call  Twitter resource string
     * @param array $postParams  POST parameters to send
     * @param array $getParams  GET parameters to send
     * @return mixed  Output with selected format
     * @throws TwitterException
     */
    public function post($call, array $postParams = null, array $getParams = null)
    {
        $this->method = 'POST';

        $this->call = $call;

        if ($postParams !== null && is_array($postParams)) {
            $this->postParams = $postParams;
        }

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        $response = $this->getResponse();

        $this->findExceptions($response);

        $this->headers = $response['headers'];

        unset($call, $postParams, $getParams);

        if(in_array($this->call, $this->nonJsonEndpoints)) {
            parse_str($response['body'], $data);
            return $data;
        }

        return $this->serializer->format($response['body']);
    }


    /**
     * Returns raw response body
     *
     * @return array
     * @throws Exception\CurlException
     */
    protected function getResponse()
    {
        $url = $this->getUrl();

        $params = array(
            'get' => $this->getParams,
            'post' => $this->postParams,
            'headers' => $this->buildRequestHeader(),
        );
        return $this->curl->send($url, $params);
    }

    /**
     * Processing Twitter Exceptions in case of error
     *
     * @param array $response  Raw response
     * @throws TwitterException
     */
    protected function findExceptions($response)
    {
        $response = $response['body'];

        if(in_array($this->call, $this->nonJsonEndpoints)) {
            return;
        }

        if (isset($response[0]) && $response[0] !== '{' && $response[0] !== '[' && !$data) {
            throw new TwitterException($response, 0);
        }

        if (!empty($data['errors']) || !empty($data['error'])) {
            if (!empty($data['errors'])) {
                $data = current($data['errors']);
            }

            if (empty($data['message']) && !empty($data['error'])) {
                $data['message'] = $data['error'];
            }

            if (!isset($data['code']) || empty($data['code'])) {
                $data['code'] = 0;
            }

            throw new TwitterException($data['message'], $data['code']);
        }

        unset($response, $data);
    }

    /**
     * Build a multipart message
     *
     * @param string $mimeBoundary  MIME boundary ID
     * @param string $filename  File location
     * @return string  Multipart message
     */
    protected function buildMultipart($mimeBoundary, $filename)
    {
        $binary = $this->getBinaryFile($filename);

        $details = pathinfo($filename);

        $type = $this->supportedMimes($details['extension']);

        $data = '--' . $mimeBoundary . static::EOL;
        $data .= 'Content-Disposition: form-data; name="media"; filename="' . $details['basename'] . '"' . static::EOL;
        $data .= 'Content-Type: ' . $type . static::EOL . static::EOL;
        $data .= $binary . static::EOL;
        $data .= '--' . $mimeBoundary . '--' . static::EOL . static::EOL;

        unset($mimeBoundary, $filename, $binary, $details, $type);

        return $data;
    }

    /**
     * Twitter supported MIME types for media upload
     *
     * @param string $mime  File extension
     * @return mixed  MIME type
     * @throws UnsupportedMime
     */
    protected function supportedMimes($mime)
    {
        $mimes = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
        );

        if (isset($mimes[$mime])) {
            return $mimes[$mime];
        }

        throw new UnsupportedMime;
    }

    /**
     * Get binary data of a file
     *
     * @param string $filename  File location
     * @return string
     * @throws FileNotFound
     * @throws FileNotReadable
     */
    protected function getBinaryFile($filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFound;
        }

        if (!is_readable($filename)) {
            throw new FileNotReadable;
        }

        ob_start();

        readfile($filename);

        $binary = ob_get_contents();

        ob_end_clean();

        unset($filename);

        return $binary;
    }
}

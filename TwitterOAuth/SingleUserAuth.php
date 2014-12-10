<?php

/**
 * TwitterOAuth - https://github.com/abfan1127/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Eric Cope <eric.cope@gmail.com>
 * @copyright 2014
 *
 * Single-user OAuth with Examples
 * https://dev.twitter.com/oauth/overview/single-user
 *
 * Tokens from dev.twitter.com
 * https://dev.twitter.com/oauth/overview/application-owner-access-tokens
 */

namespace TwitterOAuth;

class SingleUserAuth extends SingleUser
{

    public function requestOAuthToken()
    {
        $postParams = [
            'oauth_callback' => $this->getConfig('oauth_callback')
        ];
        $results = $this->post('oauth/request_token', $postParams);
        return $results;
    }
}
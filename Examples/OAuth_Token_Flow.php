<?php

require __DIR__ . '/../vendor/autoload.php';

use TwitterOAuth\SingleUserAuth;

/**
 * Needed if you select a non default serializer
 */
use TwitterOAuth\Serializer\ArraySerializer;


date_default_timezone_set('UTC');


/**
 * Array with the OAuth tokens provided by Twitter
 *
 * - consumer_key        Twitter API key
 * - consumer_secret     Twitter API secret
 * - oauth_callback      Your URL when the user authenticates with Twitter
 */


$config = array(
    'consumer_key'    => 'xvz1evFS4wEEPTGEFPHBog',
    'consumer_secret' => 'L8qq9PZyRg6ieKGEKhZolGC0vJWLw8iEJ88DRdyOg',
    'oauth_callback'  => ''
);

/**
 * Instantiate SingleUser
 */
$lib = new SingleUserAuth($config);

$results = $lib->requestOAuthToken();

//print_r($results);

// this should redirect to Twitter's authentication service.
// the authentication service authenticate the user then redirect to your callback above
//
header('Location: https://api.twitter.com/oauth/authenticate?oauth_token=' . $results['oauth_token']);
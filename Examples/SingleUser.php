<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use TwitterOAuth\SingleUser;

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
 * - oauth_token         Twitter Access token
 * - oauth_token_secret  Twitter Access token secret
 */
$config = array(
    'consumer_key' => 'xvz1evFS4wEEPTGEFPHBog',
    'consumer_secret' => 'L8qq9PZyRg6ieKGEKhZolGC0vJWLw8iEJ88DRdyOg',
    'oauth_token' => 'e98c603b55646a6d22249d9b0096e9af29bafcc2',
    'oauth_token_secret' => '07cfdf42835998375e71b46d96b4488a5c659c2f',
);

/**
 * Instantiate SingleUser
 */
$lib = new SingleUser($config);

/**
 * For different output formats you can set one of available serializers
 * (Array, Json, Object, Text, Or a custom one)
 */
$lib = new SingleUser($config, new ArraySerializer());


/**
 * Returns a collection of the most recent Tweets posted by the user
 * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
 */
$params = array(
    'screen_name' => 'ricard0per',
    'count' => 3,
    'exclude_replies' => true
);

/**
 * Send a GET call with set parameters
 */
$response = $lib->get('statuses/user_timeline', $params);

echo '<pre>'; print_r($lib->getHeaders()); echo '</pre>';

echo '<pre>'; print_r($response); echo '</pre><hr />';

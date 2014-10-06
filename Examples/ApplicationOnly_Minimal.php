<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use TwitterOAuth\ApplicationOnly;

/**
 * Needed if you select a non default serializer
 */
use TwitterOAuth\Serializer\ArraySerializer;


date_default_timezone_set('UTC');


/**
 * Array with the OAuth tokens provided by Twitter
 *
 * - consumer_key     Twitter API key
 * - consumer_secret  Twitter API secret
 */
$config = array(
    'consumer_key' => 'xvz1evFS4wEEPTGEFPHBog',
    'consumer_secret' => 'L8qq9PZyRg6ieKGEKhZolGC0vJWLw8iEJ88DRdyOg',
);

/**
 * Instantiate ApplicationOnly
 *
 * NOTE: Object Serializer are selected by default
 */
$lib = new ApplicationOnly($config);

/**
 * For different output formats you can set one of available serializers
 * (Array, Json, Object, Text, Or a custom one)
 */
$lib = new ApplicationOnly($config, new ArraySerializer());


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

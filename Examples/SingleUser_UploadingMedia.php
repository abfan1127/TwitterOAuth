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
 * To post something with media, first you need to upload some media
 * and get the ids given by Twitter
 *
 * https://dev.twitter.com/rest/public/uploading-media-multiple-photos
 */
$response = $lib->postMedia('media/upload', './photo1.jpeg');
$media_ids[] = $response['media_id'];

$response = $lib->postMedia('media/upload', './photo2.jpg');
$media_ids[] = $response['media_id'];

$response = $lib->postMedia('media/upload', './photo3.png');
$media_ids[] = $response['media_id'];


/**
 * Now you can post something with the media ids given by Twitter
 *
 * https://dev.twitter.com/rest/reference/post/statuses/update
 */
$params = array(
    'status' => 'This is a media/upload test...',
    'media_ids' => implode(',', $media_ids),
);

$response = $lib->post('statuses/update', $params);


echo '<pre>'; print_r($lib->getHeaders()); echo '</pre>';

echo '<pre>'; print_r($response); echo '</pre><hr />';

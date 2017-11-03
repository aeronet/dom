<?php

require_once 'sdk/Dailymotion.php';

// Account settings
$apiKey        = 'fc7c94354c254c7f881c';
$apiSecret     = 'b4108bfdf580d9f582984b18f29527aaf0c27629';
$testUser      = 'aeronet.biz.id@gmail.com';
$testPassword  = 'n0f12146412@';
$videoTestFile = __DIR__.'/video/test.mp4';

// Scopes you need to run your tests
$scopes = array(
    'userinfo',
    'feed',
    'manage_videos',
);
// Dailymotion object instanciation
$api = new Dailymotion();
$api->setGrantType(
    Dailymotion::GRANT_TYPE_PASSWORD,
    $apiKey,
    $apiSecret,
    $scopes,
    array(
        'username' => $testUser,
        'password' => $testPassword,
    )
);


$url = $api->uploadFile($videoTestFile);

$result = $api->post(
    '/videos',
    array(
        'url'       => $url,
        'title'     => 'Dailymotion PHP SDK upload test',
        'tags'      => 'dailymotion,api,sdk,test',
        'channel'   => 'videogames',
        'published' => true,
    )
);
var_dump($result);

print_r($url);

?>
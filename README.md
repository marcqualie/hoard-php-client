# Hoard PHP Client

This is the client library to track events and get information back from a Hoard server


## Usage

``` php
$hoard = new Hoard\Client(array(
    'server' => 'http://username.hoardhq.com';
    'apikey' => 'XXX'
));
$bucket = $hoard->getBucket('analytics');
$response = $bucket->track('pageview', array(
    'uri' => '/'
));
echo 'Tracking ID: ' . (String) $response['_id'];
```


## Silex Service Provider

``` php
$app->register(new Silex\Provider\HoardServiceProvider(), array(
    'hoard.server' => 'XXX',
    'hoard.apikey' => 'XXX',
    'hoard.bucket' => 'XXX'
));
```

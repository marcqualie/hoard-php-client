# Hoard PHP Client

This is the client library to track events and get information back from a Hoard server


## Usage

``` php
$hoard = new Hoard\Client(array(
    'server' => 'http://username.hoardhq.com';
    'apikey' => 'XXX'
));
$hoard->setDriver(new Hoard\Driver\GearmanDriver());
$bucket = $hoard->getBucket('analytics');
$response = $bucket->track('pageview', array(
    'uri' => '/'
));
echo 'Tracking ID: ' . $response->id;
```


## Silex Service Provider

``` php
$app->register(new Silex\Provider\HoardServiceProvider(), array(
    'hoard.server' => 'http://username.hoardhq.com',
    'hoard.apikey' => 'XXX',
));
$app['hoard']->setDriver(new Hoard\Driver\GearmanDriver());
$bucket = $app['hoard']->getBucket('analytics');
$response = $bucket->track('pageview', array(
    'uri' => '/'
));
echo 'Tracking ID: ' . $response->id;

```

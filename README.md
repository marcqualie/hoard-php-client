# Hoard PHP Client

This is the client library to track events and get information back from a Hoard server


## Installation

#### Via Composer

Put this code in your composer.json file

``` php
{
    "require": {
        "marcqualie/hoard": "dev-master"
    }
}
```


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
    'hoard.apikey' => 'XXX'
));
$bucket = $app['hoard']->getBucket('analytics');
$response = $bucket->track('pageview', array(
    'uri' => '/'
));
echo 'Tracking ID: ' . $response->id;
```


## Drivers

By default Hoard will use a HTTP driver, but you can extends the client and add your own

``` php
$hoard = new Hoard\Client(array(
    'server' => 'http://username.hoardhq.com',
    'apikey' => 'XXX'
));
$driver = new Hoard\Driver\GearmanDriver(array(
    'host' => 'localhost',
    'port' => 4730
));
$hoard->setDriver($driver);
$response = $hoard->track('pageview', array(
    'uri' => '/'
));
echo 'Tracking ID: ' . $response->id;
```

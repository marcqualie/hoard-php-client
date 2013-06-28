<?php

namespace Hoard\Driver;
use Hoard\Utils;
use Hoard\Exception;
use Hoard\Event\Payload;
use Hoard\Event\Response;

class GearmanDriver implements DriverInterface {

    public $gearman = false;
    public function __construct($config = array())
    {
        if ( ! class_exists('GearmanClient')) {
            throw new Exception('Gearman driver is not installed.');
        }

        if ( ! isset($config['server'])) {
            $config['server'] = '127.0.0.1';
        }

        if ( ! isset($config['port'])) {
            $config['port'] = 4730;
        }

        $this->gearman = new GearmanClient();
        $this->gearman->addServer($config['server'], $config['port']);
    }

    public function track($event, array $data = array(), array $options = array())
    {
        // Get external interfaces
        $options = $this->setOptions($options);
        $client = $this->client;
        $server = $client->getServer();
        $apikey = $client->getApiKey();

        // Payload (will verify input)
        $payload = new Payload($client->getBucket(), $event, $data);
        $post = $payload->asJSON();

        $gearmanMethod = 'doBackground';
        if (isset($options['async']) && ! $options['async'])
        {
            $gearmanMethod = 'doNormal';
        }

        $job = 'track';
        if (isset($options['job']))
        {
            $job = $options['job'];
        }

        $response = $client->$gearmanMethod($job, $post);
        return new Response(Response::OK, 'Event Added To Queue');
    }
}
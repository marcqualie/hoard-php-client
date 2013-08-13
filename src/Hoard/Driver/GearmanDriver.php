<?php

namespace Hoard\Driver;
use Hoard\Utils;
use Hoard\Exception;
use Hoard\Event\Payload;
use Hoard\Event\Response;

class GearmanDriver extends BaseDriver {

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

        $this->gearman = new \GearmanClient();
        $this->gearman->addServer($config['server'], $config['port']);

        if ( ! $this->gearman->ping('pong'))
        {
            throw new Exception('Gearman is not running');
        }
    }

    public function track($bucket, $event, array $data = array(), array $options = array())
    {
        // Get external interfaces
        $options = $this->setOptions($options);
        $client = $this->client;
        $server = $client->getServer();
        $apikey = $client->getApiKey();

        // Payload (will verify input)
        $payload = new Payload($bucket, $event, $data);
        $post = $payload->asJSON();

        // Set the gearman method depending on async or not
        $gearmanMethod = 'doBackground';
        if (isset($options['async']) && ! $options['async'])
        {
            $gearmanMethod = 'doNormal';
        }

        // Set the tracking job key, this must match what is set in Hoard UI.
        $job = 'track';
        if (isset($options['job']))
        {
            $job = $options['job'];
        }

        // Send the job to gearman
        $response = $this->gearman->$gearmanMethod($job, $post);
        return new Response(Response::OK, 'Event Added To Queue');
    }
}

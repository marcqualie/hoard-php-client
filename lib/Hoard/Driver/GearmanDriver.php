<?php

namespace Hoard\Driver;
use Hoard\Utils;
use Hoard\Exception;

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
        $gearmanMethod = 'doBackground';
        if (isset($options['async']) && ! $options['async'])
        {
            $gearmanMethod = 'doNormal';
        }

        $response = $client->$gearmanMethod('track', array(
            'event' => $event,
            'data' => $data
        ));
        return array(
            'ok' => (int) $response,
            'response' => $response
        );
    }
}
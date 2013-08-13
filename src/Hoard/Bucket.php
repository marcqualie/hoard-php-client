<?php

namespace Hoard;
use Hoard\Client;

class Bucket
{

    protected $client;
    protected $name;

    public function __construct(Client $client, $name)
    {
        $this->client = $client;
        $this->name = $name;
    }


    /**
     * Get Bucket Name
     * @return String Name of the bucket
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Get client instance
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * Track Event
     *
     * Passes tracking data directly through to driver
     * @return  Array Response data for the tracking request
     */
    public function track($event, array $data = array(), array $options = array()) {
        return $this->client->driver->track($this, $event, $data, $options);
    }

}

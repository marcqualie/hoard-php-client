<?php

namespace Hoard;

class Bucket
{

    protected $client;
    protected $name;

    public function __construct($client, $name)
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
     * Track Event
     *
     * Passes tracking data directly through to driver
     * @return  Array Response data for the tracking request
     */
    public function track($event, array $data = array(), array $options = array()) {
        return $this->client->driver->track($event, $data, $options);
    }

}

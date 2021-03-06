<?php

namespace Hoard;
use Hoard\Exception;
use Hoard\Driver\BaseDriver;
use Hoard\Driver\HttpDriver;

class Client {

    const VERSION = '0.1.0';

    protected $instance_id = null;
    protected $server = 'https://demo.hoardhq.com';
    protected $apikey = '';
    protected $buckets = array();


    /**
     * Create Instance of Hoard\Client
     * @param Array $options Array of options for connecting to Hoard cluster
     */
    public function __construct(array $options = array())
    {
        $this->instance_id = uniqid();
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        $this->setDriver(new HttpDriver());
    }


    /**
     * Get Server String
     */
    public function getServer()
    {
        return $this->server;
    }


    /**
     * Get API Key
     */
    public function getApiKey()
    {
        return $this->apikey;
    }


    /**
     * Get Bucket Instance
     * @param  String $slug       Slug for the bucket
     * @return Hoard\Bucket       Bucket Instance
     */
    public function getBucket($slug = null)
    {
        if ($slug === null) {
            throw new Exception('No bucket name specified');
        }
        if (isset($this->buckets[$slug])) {
            return $this->buckets[$slug];
        }
        $bucket = new Bucket($this, $slug);
        $this->buckets[$bucket->getName()] = $bucket;
        return $bucket;
    }


    /**
     * Get Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }


    /**
     * Set Driver
     */
    public function setDriver(BaseDriver $driver)
    {
        $this->driver = $driver;
        $this->driver->client = $this;
    }

}

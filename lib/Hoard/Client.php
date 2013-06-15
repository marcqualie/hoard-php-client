<?php

namespace Hoard;

class Client {

    protected $instance_id = null;
    public $server = 'https://demo.hoardhq.com';
    public $apikey = '';


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
    }


    /**
     * Get Bucket Instance
     * @param  String $slug       Slug for the bucket
     * @return Hoard\Bucket       Bucket Instance
     */
    public function getBucket($slug)
    {
        $bucket = new Bucket($this, $slug);
        return $bucket;
    }

}

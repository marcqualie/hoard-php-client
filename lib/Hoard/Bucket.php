<?php

namespace Hoard;

class Bucket
{

    public $client;
    public $name;

    public function __construct($client, $name)
    {
        $this->client = $client;
        $this->name = $name;
    }


    /**
     * Track Event
     */
    public function track($event_name, $meta_data) {
        return array(
            '_id' => new \MongoId()
        );
    }

}

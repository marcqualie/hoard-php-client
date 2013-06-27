<?php

namespace Hoard\Event;
use Hoard\Exception;

class Payload
{

    protected $valid = false;
    protected $bucket = null;
    protected $event = null;
    protected $data = array();


    /**
     *
     */
    public function __construct($bucket, $event, array $data = array())
    {

        // Set internal vars
        $event = trim(str_replace(' ', '-', strtolower($event)));
        $this->bucket = $bucket;
        $this->event = $event;
        $this->data = $data;

        // Verify Data
        if (! $event) {
            throw new Exception('Event name is required');
        }
        if (! preg_match('/^[a-z0-9\-\.]+$/i', $event)) {
            throw new Exception('Invalid syntax for Event Name');
        }

    }


    /**
     *
     */
    public function asArray()
    {
        return array(
            'v' => 1,
            't' => microtime(true),
            'e' => $this->event,
            's' => $this->bucket->getName(),
            'd' => $this->data
        );
    }


    /**
     *
     */
    public function asJSON()
    {
        $array = $this->asArray();
        return json_encode($array);
    }

}

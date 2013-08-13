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
        if (! preg_match('/^[a-z0-9_\-\.]+$/i', $event)) {
            throw new Exception('Invalid syntax for Event Name');
        }

    }


    /**
     * Return payload for use in drivers
     */
    public function asArray()
    {
        $payload = array(
            'v' => 1,
            't' => microtime(true),
            'b' => $this->bucket->getName(),
            'e' => $this->event,
            'd' => $this->data
        );
        return $payload;
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

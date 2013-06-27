<?php

namespace Hoard\Event;

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
        $this->bucket = $bucket;
        $this->event = $event;
        $this->data = $data;
    }


    /**
     *
     */
    public function asArray()
    {
        return array(
            'meta' => array(
                'time' => time()
            ),
            'event' => $this->event,
            'bucket' => $this->getName(),
            'data' => $this->data
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

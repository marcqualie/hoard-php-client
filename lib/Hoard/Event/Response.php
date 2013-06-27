<?php

namespace Hoard\Event;

class Response
{

    // Status Codes
    const OK = 1;
    const FAIL = 2;
    const TIMEOUT = 4;

    // Internal Variables
    protected $_ok = 0;
    protected $_code = 0;
    protected $_message = '';


    /**
     *
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }


    /**
     *
     */
    public function __get($key)
    {
        $property = '_' . $key;
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
    }

}

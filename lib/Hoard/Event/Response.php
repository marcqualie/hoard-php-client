<?php

namespace Hoard\Event;

class Response
{

    // Status Codes
    const OK = 1;
    const ERROR = 2;
    const TIMEOUT = 4;

    // Internal Variables
    protected $_ok = 0;
    protected $_code = 0;
    protected $_message = '';
    protected $_debug = array();


    /**
     *
     */
    public function __construct($code, $message, array $debug = array())
    {
        $this->_code = $code;
        $this->_message = $message;
        $this->_debug = $debug;
        if ($code === self::OK) {
            $this->_ok = 1;
        }
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

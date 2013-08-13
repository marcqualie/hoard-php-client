<?php

namespace Hoard\Driver;
use Hoard\Utils;
use Hoard\Exception;

class BaseDriver
{

    // Default Options
    protected $default_options = array(
        'async' => true
    );


    /**
     * Event Tracker
     *
     * Track events using a specified driver
     * @param  Hoard\Bucket $bucket   Bucket instance
     * @param  string $event   Name of the event
     * @param  array  $data    Event data
     * @param  array  $options Driver settings
     * @return Hoard\Event\Response
     */
    public function track($bucket, $event, array $data = array(), array $options = array())
    {

    }


    /**
     * Set driver options
     * @param  array $options Custom driver options
     * @return array
     */
    public function setOptions($options)
    {
        return array_merge(
            $this->default_options,
            $options
        );
    }

}

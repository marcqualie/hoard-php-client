<?php

namespace Hoard\Driver;

interface DriverInterface {

    public function track($event, array $data = array(), array $options = array());

}

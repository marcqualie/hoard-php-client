<?php

namespace Hoard\Test;
use Hoard\Client;
use Hoard\Driver\HttpDriver;

class ClientTest extends TestCase
{


    /**
     * Client can be initialized without params
     */
    public function testClientInitializeBlank()
    {
        $hoard = new Client;
        $this->assertEquals('https://demo.hoardhq.com', $hoard->getServer());
        $this->assertEquals('', $hoard->getApiKey());
    }


    /**
     * Client can be initialized with options
     */
    public function testClientInitializeOptions()
    {
        $options = array(
            'server' => 'http://demo.hoardhq.com',
            'apikey' => '123456',
        );
        $hoard = new Client($options);
        $this->assertEquals($options['server'], $hoard->getServer());
        $this->assertEquals($options['apikey'], $hoard->getApiKey());
    }


    /**
     * Default driver should be HTTP
     */
    public function testClientDefaultDriver()
    {
        $hoard = new Client;
        $this->assertTrue($hoard->getDriver() instanceof HttpDriver);

    }
}

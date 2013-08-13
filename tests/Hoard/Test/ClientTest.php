<?php

namespace Hoard\Test;
use Hoard\Client;
use Hoard\Bucket;
use Hoard\Driver\HttpDriver;

class ClientTest extends TestCase
{


    /**
     * Client can be initialized without params
     */
    public function testClientInitializeBlank()
    {
        $client = new Client;
        $this->assertEquals('https://demo.hoardhq.com', $client->getServer());
        $this->assertEquals('', $client->getApiKey());
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
        $client = new Client($options);
        $this->assertEquals($options['server'], $client->getServer());
        $this->assertEquals($options['apikey'], $client->getApiKey());
    }


    /**
     * Default driver should be HTTP
     */
    public function testClientDefaultDriver()
    {
        $client = new Client;
        $this->assertTrue($client->getDriver() instanceof HttpDriver);
    }


    /**
     * Make sure buckets are set and read properly
     */
    public function testDefaultBucket()
    {
        $client = new Client;
        $this->assertEquals(null, $client->getBucket());
    }


    /**
     * Buckets should be able to be set by name
     */
    public function testSetBucket()
    {
        $client = new Client;
        $bucket = new Bucket($client, 'test-bucket');
        $client->setBucket($bucket);
        $this->assertEquals($bucket, $client->getBucket());
    }
}

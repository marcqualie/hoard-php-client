<?php

namespace Hoard\Test;
use Hoard\Client;
use Hoard\Bucket;
use Hoard\Driver\HttpDriver;
use Hoard\Exception;

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
     * @expectedException Exception
     */
    public function testDefaultBucket()
    {
        $client = new Client;
        $client->getBucket();
    }


    /**
     * Buckets should be able to be set by name
     */
    public function testSetBucket()
    {
        $client = new Client;
        $bucket1 = new Bucket($client, 'test-bucket-1');
        $bucket2 = new Bucket($client, 'test-bucket-2');
        $this->assertEquals($bucket1, $client->getBucket('test-bucket-1'));
        $this->assertEquals($bucket2, $client->getBucket('test-bucket-2'));
    }
}

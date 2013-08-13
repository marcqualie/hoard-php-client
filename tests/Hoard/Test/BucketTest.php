<?php

namespace Hoard\Test;
use Hoard\Client;
use Hoard\Bucket;

class BucketTest extends TestCase
{


    /**
     * Make sure bucket is initialized properly
     */
    public function testBucketInitialize()
    {
        $client = new Client;
        $bucket = new Bucket($client, 'test-bucket');
        $this->assertEquals('test-bucket', $bucket->getName());
        $this->assertEquals($client, $bucket->getClient());
    }
}

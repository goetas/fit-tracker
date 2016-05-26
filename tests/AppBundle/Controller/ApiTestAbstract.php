<?php

namespace AppBundle\Tests\AppBundle\Controller;

use AppBundle\Tests\AppBundle\Fixtures\Loader;
use AppBundle\Tests\AppBundle\TestClient\Client;

abstract class ApiTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @param array $options
     * @return Client
     */
    protected static function getClient(array $options = array())
    {
        $client = new Client(array_merge([
            'base_uri' => 'http://localhost/app_dev.php/',
            'verify' => false,
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate',
                'Accept' => 'application/json',
            ],
        ], $options));

        return $client;
    }

    public static function setUpBeforeClass()
    {
        self::$client = self::getClient();
    }
}
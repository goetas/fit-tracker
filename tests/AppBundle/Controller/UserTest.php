<?php

namespace AppBundle\Tests\AppBundle\Controller;

use AppBundle\Tests\AppBundle\TestListener\FixtureLoader;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends ApiTestAbstract
{
    protected static $cookies;
    protected static $admin;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $auth = self::$client->post('api/auth/check', [
            'json' => ['email' => FixtureLoader::$fixtures['admin']->getEmail(), 'password' => 'password'],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        self::assertEquals(Response::HTTP_OK, $auth->getStatusCode(), $auth->getContent());
        self::$admin = json_decode($auth->getContent(), true);
        self::$cookies = $auth->headers->getCookies();
    }

    public function testList()
    {
        $userResponse = self::$client->get(self::$admin['_links']['list']['href'], [
            'cookies' => self::$cookies
        ]);
        $this->assertEquals(Response::HTTP_OK, $userResponse->getStatusCode(), $userResponse->getContent());

        $users = json_decode($userResponse->getContent(), true);

        $this->assertTrue(is_array($users));
        $this->assertGreaterThanOrEqual(3, count($users));
    }

    public function testGet()
    {
        $userResponse = self::$client->get(self::$admin['_links']['self']['href'], [
            'cookies' => self::$cookies
        ]);
        $this->assertEquals(Response::HTTP_OK, $userResponse->getStatusCode(), $userResponse->getContent());

        $user = json_decode($userResponse->getContent(), true);

        $this->assertEquals($user['id'], FixtureLoader::$fixtures['admin']->getId());
        $this->assertArrayNotHasKey('password', $user);
        $this->assertArrayHasKey('_links', $user);
    }

    public function testPut()
    {
        $userResponse = self::$client->put(self::$admin['_links']['add']['href'], [
            'cookies' => self::$cookies,
            'json' => [
                'name' => 'John',
                'email' => 'user@example.com',
                'password' => 'password'
            ]
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $userResponse->getStatusCode(), $userResponse->getContent());

        $user = json_decode($userResponse->getContent(), true);

        $this->assertEquals($user['name'], 'John');
        $this->assertEquals($user['email'], 'user@example.com');
        $this->assertArrayHasKey('_links', $user);

        return $user;
    }

    /**
     * @depends testPut
     * @param array $user
     * @return array
     */
    public function testPost(array $user)
    {
        $userResponse = self::$client->post($user['_links']['self']['href'], [
            'cookies' => self::$cookies,
            'json' => [
                'email' => 'john@example.com',
            ]
        ]);
        $this->assertEquals(Response::HTTP_OK, $userResponse->getStatusCode(), $userResponse->getContent());

        $user = json_decode($userResponse->getContent(), true);

        $this->assertEquals($user['email'], 'john@example.com');
        $this->assertEquals($user['name'], 'John');
        $this->assertArrayHasKey('_links', $user);

        return $user;
    }

    /**
     * @depends testPost
     * @param array $user
     */
    public function testDelete(array $user)
    {
        $userResponse = self::$client->delete($user['_links']['self']['href'], [
            'cookies' => self::$cookies,
        ]);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $userResponse->getStatusCode(), $userResponse->getContent());
    }

}
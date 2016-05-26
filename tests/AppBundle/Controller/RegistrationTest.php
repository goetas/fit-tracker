<?php

namespace AppBundle\Tests\AppBundle\Controller;

use AppBundle\Tests\AppBundle\TestListener\FixtureLoader;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends ApiTestAbstract
{
    protected $cookies;
    protected $admin;

    public function testRegister()
    {
        $userResponse = self::$client->put("/api/register", [
            'json' => [
                'name' => 'John',
                'email' => 'user@example.com',
                'password' => ['first' => 'password', 'second' => 'password'],
                'terms' => '1'
            ]
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $userResponse->getStatusCode(), $userResponse->getContent());

        $user = json_decode($userResponse->getContent(), true);

        $this->assertEquals($user['name'], 'John');
        $this->assertEquals($user['email'], 'user@example.com');
        $this->assertArrayHasKey('_links', $user);

        $this->assertNotEmpty($userResponse->headers->getCookies());

        $this->cookies = $userResponse->headers->getCookies();
        return $user;
    }

    /**
     * @depends testRegister
     * @param array $user
     */
    public function XtestGet(array $user)
    {
        $userResponse = self::$client->get($user['_links']['self']['href'], [
            'cookies' => $this->cookies
        ]);
        $this->assertEquals(Response::HTTP_OK, $userResponse->getStatusCode(), $userResponse->getContent());

        $user = json_decode($userResponse->getContent(), true);

        $this->assertEquals($user['name'], 'John');
        $this->assertEquals($user['email'], 'user@example.com');
        $this->assertArrayNotHasKey('password', $user);
        $this->assertArrayHasKey('_links', $user);
    }


    public function testWrongLogin()
    {
        $userResponse = self::$client->post("/api/auth/check", [
            'json' => [
                'email' => 'wrong@example.com',
                'password' => 'wrong_password'
            ]
        ]);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $userResponse->getStatusCode(), $userResponse->getContent());

        $userResponse = self::$client->post("/api/auth/check", [
            'json' => [
                'email' => FixtureLoader::$fixtures['admin']->getEmail(),
                'password' => 'wrong_password'
            ]
        ]);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $userResponse->getStatusCode(), $userResponse->getContent());

        $userResponse = self::$client->post("/api/auth/check", [
            'json' => [
                'email' => FixtureLoader::$fixtures['admin']->getEmail()
            ]
        ]);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $userResponse->getStatusCode(), $userResponse->getContent());


        $userResponse = self::$client->post("/api/auth/check", [
            'json' => []
        ]);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $userResponse->getStatusCode(), $userResponse->getContent());

        $userResponse = self::$client->post("/api/auth/check");
        $this->assertEquals(Response::HTTP_FORBIDDEN, $userResponse->getStatusCode(), $userResponse->getContent());
    }

    /**
     * @depends testRegister
     */
    public function testLogin()
    {
        $userResponse = self::$client->post("/api/auth/check", [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'password'
            ]
        ]);
        $this->assertEquals(Response::HTTP_OK, $userResponse->getStatusCode(), $userResponse->getContent());

        $user = json_decode($userResponse->getContent(), true);

        $this->assertEquals($user['name'], 'John');
        $this->assertEquals($user['email'], 'user@example.com');
        $this->assertArrayNotHasKey('password', $user);
        $this->assertArrayHasKey('_links', $user);

        return $user;
    }

    /**
     * @depends testLogin
     * @param array $user
     */
    public function testDelete(array $user)
    {
        $auth = self::$client->post('api/auth/check', [
            'json' => ['email' => FixtureLoader::$fixtures['admin']->getEmail(), 'password' => 'password'],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $this->assertEquals(Response::HTTP_OK, $auth->getStatusCode(), $auth->getContent());
        $cookies = $auth->headers->getCookies();

        $done = self::$client->delete($user['_links']['self']['href'],[
            'cookies' => $cookies
        ]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $done->getStatusCode(), $done->getContent());
    }
}
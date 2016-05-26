<?php

namespace AppBundle\Tests\AppBundle\Controller;

use AppBundle\Tests\AppBundle\TestListener\FixtureLoader;

class ActivityTest extends ApiTestAbstract
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
        self::assertEquals(200, $auth->getStatusCode(), $auth->getContent());
        self::$admin = json_decode($auth->getContent(), true);
        self::$cookies = $auth->headers->getCookies();

    }

    public function XtestGetAsNotAdmin()
    {
        $auth = self::$client->post('api/auth/check', [
            'json' => ['email' => FixtureLoader::$fixtures['user1']->getEmail(), 'password' => 'password'],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $activitiesResponse = self::$client->get('/api/activity/' . FixtureLoader::$fixtures['user1']->getId() . "/", [
            'cookies' => $auth->headers->getCookies()
        ]);
        $this->assertEquals(200, $activitiesResponse->getStatusCode(), $activitiesResponse->getContent());

        $activitiesResponse = self::$client->get('/api/activity/' . FixtureLoader::$fixtures['user2']->getId() . "/", [
            'cookies' => $auth->headers->getCookies()
        ]);
        $this->assertEquals(403, $activitiesResponse->getStatusCode(), $activitiesResponse->getContent());
    }

    public function XtestToSomebodyElseAsNotAdmin()
    {
        $auth = self::$client->post('api/auth/check', [
            'json' => ['email' => FixtureLoader::$fixtures['user1']->getEmail(), 'password' => 'password'],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $activity = self::$client->put('/api/activity/' . FixtureLoader::$fixtures['admin']->getId() . "/new", [
            'cookies' => $auth->headers->getCookies(),
            'json' => [
                'day' => '2014-01-01',
                'distance' => 5,
                'time' => 5,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(403, $activity->getStatusCode(), $activity->getContent());
    }

    public function testList()
    {
        $activities = self::$client->get(self::$admin['_links']['activities']['href'], [
            'cookies' => self::$cookies
        ]);

        $this->assertEquals(200, $activities->getStatusCode(), $activities->getContent());

        $activitiesArray = json_decode($activities->getContent(), true);

        $this->assertCount(5, $activitiesArray);

        return $activitiesArray;
    }

    public function testReportWeek()
    {
        $activities = self::$client->get(self::$admin['_links']['activities_report']['href'], [
            'cookies' => self::$cookies,
            'query' => [
            ]
        ]);

        $this->assertEquals(200, $activities->getStatusCode(), $activities->getContent());
        $report = json_decode($activities->getContent(), true);
        $this->assertGreaterThan(0, count($report));

        foreach ($report as $week) {
            $this->assertArrayHasKey('year', $week);
            $this->assertArrayHasKey('week', $week);
            $this->assertArrayHasKey('speed', $week);
        }
        return $report;
    }

    /**
     * @depends testReportWeek
     * @param array $fullReport
     */
    public function testFilteredReportWeek(array $fullReport)
    {
        $oneReport = $fullReport[0];


        $firstNext = new \DateTime();
        $firstNext->setDate($oneReport['year'], 1, 1);
        $firstNext->modify("+ " . $oneReport['week'] . " weeks");
        $firstNext->modify("+7 days");


        $activities = self::$client->get(self::$admin['_links']['activities_report']['href'], [
            'cookies' => self::$cookies,
            'query' => [
                'from' => $firstNext->format("Y-m-d")
            ]
        ]);

        $this->assertEquals(200, $activities->getStatusCode(), $activities->getContent());
        $report = json_decode($activities->getContent(), true);
        $this->assertCount(count($fullReport) - 1, $report);

        foreach ($report as $week) {
            $this->assertArrayHasKey('year', $week);
            $this->assertArrayHasKey('week', $week);
            $this->assertArrayHasKey('speed', $week);
        }
    }

    /**
     * @depends testList
     * @param array $activitiesArray
     */
    public function testListWithFilters(array $activitiesArray)
    {
        $min = min(array_map(function ($activity) {
            return new \DateTime($activity['day']);
        }, $activitiesArray));
        $max = min(array_map(function ($activity) {
            return new \DateTime($activity['day']);
        }, $activitiesArray));


        $activities = self::$client->get(self::$admin['_links']['activities']['href'], [
            'cookies' => self::$cookies,
            'query' => [
                'from' => $min->modify("-100days")->format("Y-m-d"),
                'to' => $max,
            ]
        ]);

        $this->assertEquals(200, $activities->getStatusCode(), $activities->getContent());
        $this->assertCount(5, json_decode($activities->getContent(), true));

        $activities = self::$client->get(self::$admin['_links']['activities']['href'], [
            'cookies' => self::$cookies,
            'query' => [
                'from' => $min->modify("-10days")->format("Y-m-d"),
            ]
        ]);

        $this->assertEquals(200, $activities->getStatusCode(), $activities->getContent());
        $this->assertCount(5, json_decode($activities->getContent(), true));

        $activities = self::$client->get(self::$admin['_links']['activities']['href'], [
            'cookies' => self::$cookies,
            'query' => [
                'to' => $min->modify("-10days")->format("Y-m-d"),
            ]
        ]);

        $this->assertEquals(200, $activities->getStatusCode(), $activities->getContent());
        $this->assertCount(0, json_decode($activities->getContent(), true));
    }

    public function testListSomebodyElseAsAdmin()
    {
        $activitiesResponse = self::$client->get('/api/activity/' . FixtureLoader::$fixtures['user1']->getId() . "/", [
            'cookies' => self::$cookies
        ]);
        $activitiesArray = json_decode($activitiesResponse->getContent(), true);

        $this->assertCount(10, $activitiesArray);

        return $activitiesArray;
    }

    /**
     * @depends testListSomebodyElseAsAdmin
     */
    public function testGetSomebodyElseAsAdmin($activitiesArray)
    {
        foreach ($activitiesArray as $activity) {
            $activitiesResponse = self::$client->get($activity['_links']['self']['href'], [
                'cookies' => self::$cookies
            ]);
            $this->assertEquals(200, $activitiesResponse->getStatusCode(), $activitiesResponse->getContent());
        }
    }

    /**
     * @depends testList
     */
    public function testGet(array $activitiesArray)
    {
        $activitiesResponse = self::$client->get('/api/activity/' . FixtureLoader::$fixtures['user1']->getId() . "/", [
            'cookies' => self::$cookies
        ]);
        $this->assertEquals(200, $activitiesResponse->getStatusCode(), $activitiesResponse->getContent());

        foreach ($activitiesArray as $activity) {
            $activityResponse = self::$client->get($activity['_links']['self']['href'], [
                'cookies' => self::$cookies
            ]);
            $this->assertEquals(200, $activityResponse->getStatusCode(), $activityResponse->getContent());
        }
    }

    public function testPut()
    {
        $activity = self::$client->put('/api/activity/' . FixtureLoader::$fixtures['admin']->getId() . "/new", [
            'cookies' => self::$cookies,
            'json' => [
                'day' => '2014-01-01',
                'distance' => 5,
                'time' => 5,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(201, $activity->getStatusCode(), $activity->getContent());

        $activityArray = json_decode($activity->getContent(), true);

        return $activityArray;
    }


    /**
     * @depends testPut
     */
    public function testPost($activityArray)
    {

        $activity = self::$client->post($activityArray['_links']['self']['href'], [
            'cookies' => self::$cookies,
            'json' => [
                'day' => '2014-02-01',
                'distance' => 9
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(200, $activity->getStatusCode(), $activity->getContent());

        $activityArray = json_decode($activity->getContent(), true);

        return $activityArray;
    }


    /**
     * @depends testPost
     */
    public function testDelete($activityArray)
    {
        $activity = self::$client->delete($activityArray['_links']['delete']['href'], [
            'cookies' => self::$cookies
        ]);

        $this->assertEquals(204, $activity->getStatusCode(), $activity->getContent());
    }

}
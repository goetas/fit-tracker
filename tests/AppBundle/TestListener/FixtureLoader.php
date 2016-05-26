<?php

namespace AppBundle\Tests\AppBundle\TestListener;

use AppBundle\Tests\AppBundle\Kernel\KernelUtils;

class FixtureLoader implements \PHPUnit_Framework_TestListener
{
    /**
     * @var FixturesLoader
     */
    protected $fixturesLoader;

    /**
     * @var FixturesLoader
     */
    public static $fixtures;

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        ;
    }

    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ($suite->getName() == "Project Test Suite") {
            $kernel = KernelUtils::getKernel();
            $this->fixturesLoader = $kernel->getContainer()->get('appbundle.api.test.fixtures.fixtures_loader');
            $appBundle = $kernel->getBundle('AppBundle');
            self::$fixtures = $this->fixturesLoader->load($appBundle->getPath() . "/Resources/fixtures/data.yml");
        }
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ($suite->getName() == "Project Test Suite" && self::$fixtures) {
            $this->fixturesLoader = KernelUtils::getKernel()->getContainer()->get('appbundle.api.test.fixtures.fixtures_loader');
            $this->fixturesLoader->unload(self::$fixtures);
        }
    }
}
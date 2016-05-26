<?php
namespace AppBundle\Tests\AppBundle\Fixtures;

use h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface;

class FixturesLoader
{
    /**
     * @var FixtureManagerInterface
     */
    protected $fixtureManager;

    public function __construct(FixtureManagerInterface $fixtureManager, array $providers = array())
    {
        $this->fixtureManager = $fixtureManager;

        foreach ($providers as $provider) {
            $this->fixtureManager->addProvider($provider);
        }
    }
    /**
     * @param string[]|string $files
     * @param array $options
     * @return array
     */
    public function load($files, array $options = array())
    {
        if (function_exists('xdebug_enable')) {
            xdebug_stop_code_coverage(false);
        }
        if (!is_array($files)) {
            $files = [$files];
        }
        $set = $this->fixtureManager->createFixtureSet();
        if (isset($options['seed'])) {
            $set->setSeed($options['seed']);
        }
        if (isset($options['order'])) {
            $set->setSeed($options['order']);
        }
        foreach ($files as $file) {
            $set->addFile($file, 'yaml');
        }
        $fixtures = $this->fixtureManager->load($set);
        if (function_exists('xdebug_enable')) {
            xdebug_start_code_coverage();
        }
        return $fixtures;
    }

    public function unload($entities)
    {
        if (function_exists('xdebug_enable')) {
            xdebug_stop_code_coverage(false);
        }
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        // $entities = array_reverse($entities);
        $this->fixtureManager->remove($entities);
        if (function_exists('xdebug_enable')) {
            xdebug_start_code_coverage();
        }
    }
}
<?php

namespace AppBundle\Tests\AppBundle\Kernel;

use Symfony\Component\Filesystem\Filesystem;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    public function testBoot()
    {
        $kernel = KernelUtils::getKernel();
        $cacheDir = $kernel->getContainer()->getParameter('kernel.cache_dir');
        $kernel->shutdown();
        $fs = new Filesystem();
        $fs->remove(glob($cacheDir . "/*"));
        $kernel->boot();
    }
}
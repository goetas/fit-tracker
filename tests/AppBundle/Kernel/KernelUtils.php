<?php
namespace AppBundle\Tests\AppBundle\Kernel;


class KernelUtils
{
    /**
     * @var \AppKernel
     */
    protected static $kernel;

    /**
     * @return \AppKernel
     */
    public static function getKernel()
    {
        if (!self::$kernel) {
            if (function_exists('xdebug_enable')) {
                xdebug_stop_code_coverage(false);
            }
            self::$kernel = new \AppKernel('test', !empty($GLOBALS['KERNEL_DEBUG']));
            self::$kernel->boot();

            $container = self::$kernel->getContainer();

            $warmer = $container->get('cache_warmer');
            $warmer->enableOptionalWarmers();
            $warmer->warmUp($container->getParameter('kernel.cache_dir'));
            if (function_exists('xdebug_enable')) {
                xdebug_start_code_coverage();
            }
        }
        return self::$kernel;
    }
}
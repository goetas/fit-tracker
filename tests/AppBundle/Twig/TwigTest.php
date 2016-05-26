<?php

namespace AppBundle\Tests\AppBundle\Twig;

use AppBundle\Twig\AppExtension;

class TwigTest extends \PHPUnit_Framework_TestCase
{
    public function testExt()
    {
        $extension = new AppExtension();

        $this->assertTrue(is_array($extension->getFilters()));
        $this->assertContainsOnlyInstancesOf(\Twig_SimpleFilter::class, $extension->getFilters());
        /**
         * @var $filter \Twig_SimpleFilter
         */
        $filter = $extension->getFilters()[0];
        $this->assertEquals('angularjs_class_array', $filter->getName());

        $res = call_user_func($filter->getCallable(), 'a b c');
        $this->assertEquals("'a', 'b', 'c'", $res);
    }
}
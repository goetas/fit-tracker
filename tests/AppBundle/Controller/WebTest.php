<?php

namespace AppBundle\Tests\AppBundle\Controller;

use AppBundle\Controller\WebController;
use AppBundle\Tests\AppBundle\Kernel\KernelUtils;

class WebTest extends  \PHPUnit_Framework_TestCase
{
    /**
     * @var controller
     */
    private $controller;

    public function setUp()
    {
        $this->controller = new WebController();
        $container = KernelUtils::getKernel()->getContainer();
        $this->controller->setContainer($container);
    }

    public function testGet()
    {
        $resp = $this->controller->getAction('profile');

        $this->assertContains('Profile', $resp->getContent());
    }

    public function testIndex()
    {
        $resp = $this->controller->indexAction();
        $this->assertContains('Welcome!', $resp->getContent());
        $this->assertContains('ng-view', $resp->getContent());
    }

}
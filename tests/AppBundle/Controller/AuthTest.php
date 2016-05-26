<?php

namespace AppBundle\Tests\AppBundle\Controller;

use AppBundle\Controller\AuthController;
use AppBundle\Tests\AppBundle\Kernel\KernelUtils;

class AuthTest extends  \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthController
     */
    private $controller;

    public function setUp()
    {
        $this->controller = new AuthController();
        $container = KernelUtils::getKernel()->getContainer();
        $this->controller->setContainer($container);
    }

    public function testActions()
    {
        $resp = $this->controller->logoutAction();
        $this->assertNull($resp);
    }

    public function testIndex()
    {
        $resp = $this->controller->checkAction();
        $this->assertNull($resp);
    }

}
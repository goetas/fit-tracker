<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class WebController extends Controller
{
    use NamedFormTrait;
    /**
     * @Route("/web/{page}.html", name="web_page")
     * @Method({"GET"})
     */
    public function getAction($page)
    {
        return $this->render('AppBundle:Web:' . $page . ".html.twig");
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig', ['user' => $this->getUser()]);
    }
}

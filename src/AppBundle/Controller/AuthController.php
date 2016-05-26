<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\RegisterType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class AuthController extends FOSRestController
{
    use NamedFormTrait;
    /**
     * @ApiDoc(
     *  resource=false,
     *  authentication=true,
     *  description="Log-out from the API",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  section="auth"
     * )
     * @Method({"POST"})
     * @Route("/auth/logout", name="auth_logout")
     */
    public function logoutAction()
    {

    }

    /**
     * This method will return a cookie that has to be used for all the subsequent calls.
     *
     * @ApiDoc(
     *  resource=false,
     *  description="Log-in into the API",
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Returns in case of failure"
     *  },
     *  section="auth"
     * )
     * @Method({"POST"})
     * @Route("/auth/check", name="auth_check")
     */
    public function checkAction()
    {

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ApiDoc(
     *  resource=false,
     *  authentication=true,
     *  description="Login as a different user",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\User",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="users"
     * )
     * @Route("/login-as/{id}", name="user_re_login")
     * @Method({"POST"})
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function reLoginAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($id);
        if (!$user){
            return $this->view($user, Response::HTTP_NOT_FOUND);
        }

        $token = $this->get('app.token_authenticator')->createAuthenticatedToken($user, 'login');
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *  resource=false,
     *  description="Create (register) a new user",
     *  statusCodes={
     *    201="Returned when successful",
     *    400="Returns when validation fails"
     *  },
     *  input = {
     *   "class" = "AppBundle\Form\Type\RegisterType",
     *   "name" = ""
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\User",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="users-public"
     * )
     * @Route("/register", name="user_register")
     * @Method({"PUT"})
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createNamedForm('', new RegisterType(), $user);
        $form->submit($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $token = $this->get('app.token_authenticator')->createAuthenticatedToken($user, 'login');
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->view($user, Response::HTTP_CREATED);
        } else {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }
    }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\EditUserType;
use AppBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/user")
 */
class UserController extends FOSRestController
{
    use NamedFormTrait;

    /**
     * Return an user.
     * This operation will be successful only if the user to retrieve is the same as the authenticated user
     * or the authenticated user is an admin.
     * @ApiDoc(
     *  resource=true,
     *  description="Get a user.",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      }
     *  },
     *  authentication=true,
     *  output = {
     *     "class"="AppBundle\Entity\User",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="users"
     * )
     * @Route("/{id}", name="user_get")
     * @ParamConverter("user", converter="user_param_converter", options={
     *    "param" = "id",
     * })
     * @Method({"GET"})
     * @param User $user
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(User $user)
    {
        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * Retrieve all the users.
     * @ApiDoc(
     *  resource=false,
     *  description="Get users. Admin-only",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\User",
     *     "collection"=true,
     *     "collectionName"="user",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  authentication=true,
     *  section="users"
     * )
     * @Route("/", name="user_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy([], ['id' => 'DESC']);
        return $this->view($users, Response::HTTP_OK);
    }

    /**
     * This operation will be successful only if the user to update is the same as the authenticated user
     * or the authenticated user is an admin.
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a user. Admin-only",
     *  statusCodes={
     *    200="Returned when successful",
     *    400="Returns when validation fails"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      }
     *  },
     *  authentication=true,
     *  input = {
     *   "class" = "AppBundle\Form\Type\UserType",
     *   "name" = ""
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\User",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="users"
     * )
     * @Route("/{id}", name="user_post")
     * @Method({"POST"})
     * @param User $user
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @internal param $id
     * @ParamConverter("user", converter="user_param_converter", options={
     *    "param" = "id",
     * })
     */
    public function postAction(User $user, Request $request)
    {
        $form = $this->createNamedForm('', new UserType(), $user, [
            'admin' => $this->isGranted('ROLE_ADMIN')
        ]);
        $form->submit($request, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->view($user, Response::HTTP_OK);
        } else {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create a user. Admin-only",
     *  statusCodes={
     *    201="Returned when successful",
     *    400="Returns when validation fails"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      }
     *  },
     *  authentication=true,
     *  output = {
     *     "class"="AppBundle\Entity\User",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  input = {
     *   "class" = "AppBundle\Form\Type\UserType",
     *   "name" = ""
     *  },
     *  section="users"
     * )
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}", name="user_put")
     * @Method({"PUT"})
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function putAction(Request $request)
    {
        $user = new User();

        $form = $this->createNamedForm('', new UserType(), $user, [
            'admin' => $this->isGranted('ROLE_ADMIN')
        ]);
        $form->submit($request, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);

            $this->getDoctrine()->getManager()->flush();

            return $this->view($user, Response::HTTP_CREATED);
        } else {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a user. Admin-only",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      }
     *  },
     *  authentication=true,
     *  section="users"
     * )
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}", name="user_delete")
     * @ParamConverter("user", converter="user_param_converter", options={
     *    "param" = "id",
     * })
     * @Method({"DELETE"})
     * @param User $user
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(User $user)
    {
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\User;
use AppBundle\Form\Type\ActivityType;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/activity")
 */
class ActivityController extends FOSRestController
{
    use NamedFormTrait;

    /**
     * @ApiDoc(
     *  resource=false,
     *  authentication=true,
     *  description="List all the activities for a given user",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="user",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      }
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\Activity",
     *     "collection"=true,
     *     "collectionName"="activity",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="activities"
     * )
     * @QueryParam(name="from", description="Filter by date, start", requirements="\d{4}-\d{2}-\d{2}", array=false)
     * @QueryParam(name="to", description="Filter by date, end", requirements="\d{4}-\d{2}-\d{2}", array=false)
     * @Route("/{user}/", name="activity_list")
     * @ParamConverter("user", converter="user_param_converter", options={
     *    "param" = "user",
     * })
     * @Method({"GET"})
     * @param User $user
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function listAction(User $user, ParamFetcher $paramFetcher)
    {
        $filters = $paramFetcher->all();
        $activities = $this->getDoctrine()->getRepository('AppBundle:Activity')->findByFilters(array_merge($filters, ['user' => $user]), ['day' => 'ASC']);
        return $this->view($activities);
    }

    /**
     * @ApiDoc(
     *  resource=false,
     *  authentication=true,
     *  description="Generate a report grouping activities per week",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="user",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      }
     *  },
     *  output = {
     *     "class"="AppBundle\PseudoEntity\WeeklyActivity",
     *     "collection"=true,
     *     "collectionName"="activity",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="activities"
     * )
     * @QueryParam(name="from", description="Filter by date, start", requirements="\d{4}-\d{2}-\d{2}", array=false)
     * @QueryParam(name="to", description="Filter by date, end", requirements="\d{4}-\d{2}-\d{2}", array=false)
     * @Route("/{user}/week-report", name="activity_report")
     * @ParamConverter("user", converter="user_param_converter", options={
     *    "param" = "user",
     * })
     * @Method({"GET"})
     * @param User $user
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function reportPerWeekAction(User $user, ParamFetcher $paramFetcher)
    {
        $filters = $paramFetcher->all();
        $activities = $this->getDoctrine()->getRepository('AppBundle:Activity')->findByFiltersWeekGroup(array_merge($filters, ['user' => $user]), ['id' => 'DESC']);
        return $this->view($activities);
    }

    /**
     * @Route("/{user}/{id}", name="activity_get")
     * @ParamConverter("activity", converter="activity_param_converter", options={
     *    "param" = "id",
     * })
     * @ApiDoc(
     *  resource=true,
     *  authentication=true,
     *  description="Get an activity for a user",
     *  output = {
     *     "class"="AppBundle\Entity\Activity",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="user",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      },
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the activity id"
     *      }
     *  },
     *  section="activities"
     * )
     * @Method({"GET"})
     * @param Activity $activity
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(Activity $activity)
    {
        return $this->view($activity, $activity ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    /**
     * @ParamConverter("user", converter="user_param_converter", options={
     *    "param" = "user",
     * })
     * @ApiDoc(
     *  resource=true,
     *  authentication=true,
     *  description="Add an activity for a user",
     *  input = {
     *   "class" = "AppBundle\Form\Type\ActivityType",
     *   "name" = ""
     *  },
     *  requirements={
     *      {
     *          "name"="user",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      },
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the activity id"
     *      }
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\Activity",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  statusCodes={
     *    201="Returned when successful",
     *    400="Returns when validation fails"
     *  },
     *  section="activities"
     * )
     * @Route("/{user}/{id}", name="activity_put")
     * @Method({"PUT"})
     * @param Request $request
     * @param User $user
     * @return \FOS\RestBundle\View\View
     */
    public function putAction(Request $request, User $user)
    {
        $activity = new Activity($user);

        $form = $this->createNamedForm('', new ActivityType(), $activity);
        $form->submit($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($activity);

            $this->getDoctrine()->getManager()->flush();

            return $this->view($activity, Response::HTTP_CREATED);
        } else {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  authentication=true,
     *  description="Update an activity for a user",
     *  statusCodes={
     *    200="Returned when successful",
     *    400="Returns when validation fails"
     *  },
     *  input = {
     *   "class" = "AppBundle\Form\Type\ActivityType",
     *   "name" = ""
     *  },
     *  requirements={
     *      {
     *          "name"="user",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      },
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the activity id"
     *      }
     *  },
     *  output = {
     *     "class"="AppBundle\Entity\Activity",
     *     "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *  },
     *  section="activities"
     * )
     * @Route("/{user}/{id}", name="activity_post")
     * @ParamConverter("activity", converter="activity_param_converter", options={
     *    "param" = "id",
     * })
     * @Method({"POST"})
     * @param Activity $activity
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @internal param $id
     */
    public function postAction(Activity $activity, Request $request)
    {
        $form = $this->createNamedForm('', new ActivityType(), $activity);
        $form->submit($request, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($activity);

            $this->getDoctrine()->getManager()->flush();

            return $this->view($activity, Response::HTTP_OK);
        } else {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  authentication=true,
     *  description="Delete an activity for a user",
     *  statusCodes={
     *    200="Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="user",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the user id"
     *      },
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="the activity id"
     *      }
     *  },
     *  section="activities"
     * )
     * @Route("/{user}/{id}", name="activity_delete")
     * @ParamConverter("activity", converter="activity_param_converter", options={
     *    "param" = "id",
     * })
     * @Method({"DELETE"})
     * @param Activity $activity
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(Activity $activity)
    {
        $this->getDoctrine()->getManager()->remove($activity);
        $this->getDoctrine()->getManager()->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}

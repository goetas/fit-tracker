<?php
namespace AppBundle\Param;

use AppBundle\Entity\Activity;
use AppBundle\Repository\ActivityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ActivityParamConverter implements ParamConverterInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    public function __construct(ActivityRepository $activityRepository, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->activityRepository = $activityRepository;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $idField = $configuration->getOptions()['param'];
        $id = $request->attributes->get($idField);
        $activity = $this->activityRepository->findOneBy(['id'=> $id]);

        if (!$activity){
            throw new NotFoundHttpException();
        }

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && $activity->getUser() !== $this->tokenStorage->getToken()->getUser()){
            throw new AccessDeniedHttpException();
        }
        $request->attributes->set($configuration->getName(), $activity);
        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return ($configuration->getClass() === Activity::class);
    }
}
<?php
namespace AppBundle\Param;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserParamConverter implements ParamConverterInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(UserRepository $userRepository, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage)
    {
        $this->userRepository = $userRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $idField = $configuration->getOptions()['param'];
        $id = $request->attributes->get($idField);
        $user = $this->userRepository->findOneBy(['id'=> $id]);

        if (!$user){
            throw new NotFoundHttpException();
        }

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && $user !== $this->tokenStorage->getToken()->getUser()){
            throw new AccessDeniedHttpException();
        }
        $request->attributes->set($configuration->getName(), $user);
        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return ($configuration->getClass() === User::class);
    }
}
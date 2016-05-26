<?php
namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;


class PasswordEncoder
{
    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    public function __construct(EncoderFactory $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof User) {
            $pwd = $object->getPassword();
            $passwordEncrypted = $this->encoderFactory->getEncoder($object)->encodePassword($pwd, null);
            $object->setPassword($passwordEncrypted);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof User && $args->hasChangedField('password')) {
            $old = $args->getOldValue('password');

            $new = $args->getNewValue('password');

            if ($new != $old) {
                $passwordEncrypted = $this->encoderFactory->getEncoder($object)->encodePassword($new, null);
                $args->setNewValue('password', $passwordEncrypted);
            }
        }
    }
}
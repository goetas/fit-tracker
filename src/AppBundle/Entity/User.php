<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("email")
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 *  @Hateoas\Relation("login_as",
 *          exclusion = @Hateoas\Exclusion(excludeIf = "expr(!service('security.authorization_checker').isGranted('ROLE_ADMIN'))"),
 *          href = @Hateoas\Route(
 *          "user_re_login",
 *          parameters = {
 *              "id" = "expr(object.getId())"
 *          },
 *          absolute=true
 *
 *      ))
 *  @Hateoas\Relation("add",
 *          exclusion = @Hateoas\Exclusion(excludeIf = "expr(!object.getIsAdmin())"),
 *          href = @Hateoas\Route(
 *          "user_put",
 *          parameters = {
 *              "id" = "new"
 *          },
 *          absolute=true
 *
 *      ))
 *  @Hateoas\Relation("get",
 *          exclusion = @Hateoas\Exclusion(excludeIf = "expr(!object.getIsAdmin())"),
 *          href = @Hateoas\Route(
 *          "user_get",
 *          parameters = {
 *              "id" = ":user:"
 *          },
 *          absolute=true
 *
 *      ))
 *  @Hateoas\Relation("list",
 *      exclusion = @Hateoas\Exclusion(excludeIf = "expr(!object.getIsAdmin())"),
 *          href = @Hateoas\Route(
 *          "user_list",
 *          absolute=true
 *      ))
 *
 * @Hateoas\Relation("self",
 *          href = @Hateoas\Route(
 *          "user_get",
 *          parameters = {
 *              "id" = "expr(object.getId())"
 *          },
 *          absolute=true
 *      ))
 *
 * @Hateoas\Relation("edit",
 *          href = @Hateoas\Route(
 *          "user_post",
 *          parameters = {
 *              "id" = "expr(object.getId())"
 *          },
 *          absolute=true
 *      ))
 *
 * @Hateoas\Relation("activities",
 *          href = @Hateoas\Route(
 *          "activity_list",
 *          parameters = {
 *              "user" = "expr(object.getId())"
 *          },
 *          absolute=true
 *      ))
 * @Hateoas\Relation("activities_report",
 *          href = @Hateoas\Route(
 *          "activity_report",
 *          parameters = {
 *              "user" = "expr(object.getId())"
 *          },
 *          absolute=true
 *      ))
 *
 * @Hateoas\Relation("activities_add",
 *          href = @Hateoas\Route(
 *          "activity_put",
 *          parameters = {
 *              "user" = "expr(object.getId())",
 *              "id" = "new",
 *          },
 *          absolute=true
 *      ))
 *
 * @Hateoas\Relation("activities_get",
 *          href = @Hateoas\Route(
 *          "activity_get",
 *          parameters = {
 *              "user" = "expr(object.getId())",
 *              "id" = ":activity:",
 *          },
 *          absolute=true
 *      ))
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\NotNull
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\Email
     * @Assert\NotBlank
     * @Assert\NotNull
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     * @Serializer\Exclude
     * @Assert\NotBlank
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;


    /**
     * @var \DateTime
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @ORM\Column(name="registered", type="datetime")
     */
    private $registered;

    /**
     * @var integer
     * @ORM\Column(name="is_admin", type="integer")
     */
    private $isAdmin = 0;

    /**
     * @var Activity[]
     * @Serializer\Exclude
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Activity", mappedBy="user", cascade={"remove"})
     */
    private $actvities;

    public function __construct()
    {
        $this->registered = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param int $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[]|string[] The user roles
     */
    public function getRoles()
    {
        $roles = array('ROLE_USER');
        if ($this->isAdmin) {
            $roles[] = "ROLE_ADMIN";
        }
        return $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return strval($this->id);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {

        $this->id = intval($serialized);
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User){
            return $user->id == $this->id;
        }
        return false;
    }
}


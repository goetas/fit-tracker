<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Activity
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActivityRepository")
 * @Hateoas\Relation("delete",
 *          href = @Hateoas\Route(
 *          "activity_delete",
 *          parameters = {
 *              "user" = "expr(object.getUser().getId())",
 *              "id" = "expr(object.getId())",
 *          },
 *          absolute=true
 *      ))
 * @Hateoas\Relation("self",
 *          href = @Hateoas\Route(
 *          "activity_get",
 *          parameters = {
 *              "user" = "expr(object.getUser().getId())",
 *              "id" = "expr(object.getId())",
 *          },
 *          absolute=true
 *      ))
 * @Hateoas\Relation("edit",
 *          href = @Hateoas\Route(
 *          "activity_post",
 *          parameters = {
 *              "user" = "expr(object.getUser().getId())",
 *              "id" = "expr(object.getId())",
 *          },
 *          absolute=true
 *      ))
 */
class Activity
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
     * @var User
     * @Serializer\Exclude
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="actvities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var \DateTime
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Assert\Date()
     * @Assert\NotBlank()
     * @ORM\Column(name="day", type="date")
     */
    private $day;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @ORM\Column(name="time", type="integer")
     */
    private $time;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @ORM\Column(name="distance", type="integer", length=255)
     */
    private $distance;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param \DateTime $day
     */
    public function setDay(\DateTime $day = null)
    {
        $this->day = $day;
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
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return Activity
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     *
     * @return Activity
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }
}


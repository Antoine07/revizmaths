<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="stats")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\StatRepository")
 */
class Stat
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
     * @ORM\Column(name="finished_at", type="datetime", nullable=true))
     */
    private $finisheAt;

    /**
     *  @ORM\Column( type="text", nullable=true))
     */
    private $meta;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="smallint")
     */
    private $scrore;

    /**
     * @ORM\ManyToOne(targetEntity="Reviz\FrontBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    /**
     * Set finisheAt
     *
     * @param \DateTime $finisheAt
     *
     * @return Stat
     */
    public function setFinisheAt($finisheAt)
    {
        $this->finisheAt = $finisheAt;

        return $this;
    }

    /**
     * Get finisheAt
     *
     * @return \DateTime
     */
    public function getFinisheAt()
    {
        return $this->finisheAt;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set meta
     *
     * @param string $meta
     *
     * @return Stat
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return string
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set scrore
     *
     * @param integer $scrore
     *
     * @return Stat
     */
    public function setScrore($scrore)
    {
        $this->scrore = $scrore;

        return $this;
    }

    /**
     * Get scrore
     *
     * @return integer
     */
    public function getScrore()
    {
        return $this->scrore;
    }

    /**
     * Set user
     *
     * @param \Reviz\FrontBundle\Entity\User $user
     *
     * @return Stat
     */
    public function setUser(\Reviz\FrontBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Reviz\FrontBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}

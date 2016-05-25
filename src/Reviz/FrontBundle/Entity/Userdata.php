<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Userdata
 *
 * @ORM\Table(name="userdata")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\UserdataRepository")
 */
class Userdata
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
     *
     * @ORM\Column(name="config", type="text")
     */
    private $config;


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
     * Set config
     *
     * @param string $config
     *
     * @return Userdata
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set user
     *
     * @param \Reviz\FrontBundle\Entity\User $user
     *
     * @return Userdata
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

<?php
namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Entity
 * @ORM\Entity
 * @ORM\Table(name="medias")
 */
class Image extends Media
{

    /**
     * @ORM\OneToOne(targetEntity="Reviz\FrontBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $alt;


    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set user
     *
     * @param \Reviz\FrontBundle\Entity\User $user
     *
     * @return Image
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

    public function getType()
    {
        return 'image';

    }
}

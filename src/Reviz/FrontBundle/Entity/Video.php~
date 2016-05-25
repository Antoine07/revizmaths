<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Entity
 * @ORM\Table(name="medias")
 */
class Video extends Media
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortcode;


    /**
     * Set shortcode
     *
     * @param string $shortcode
     *
     * @return Video
     */
    public function setShortcode($shortcode)
    {
        $this->shortcode = $shortcode;

        return $this;
    }

    /**
     * Get shortcode
     *
     * @return string
     */
    public function getShortcode()
    {
        return $this->shortcode;
    }

    public function getType()
    {
        return 'video';
    }


}

<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Question extends Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="Reviz\FrontBundle\Entity\Stat")
     * @ORM\JoinColumn(nullable=true)
     */
    private $stat;

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Question
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set stat
     *
     * @param \Reviz\FrontBundle\Entity\Stat $stat
     *
     * @return Question
     */
    public function setStat(\Reviz\FrontBundle\Entity\Stat $stat = null)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return \Reviz\FrontBundle\Entity\Stat
     */
    public function getStat()
    {
        return $this->stat;
    }
}

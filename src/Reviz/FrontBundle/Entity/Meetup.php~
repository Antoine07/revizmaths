<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meetup
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\MeetupRepository")
 */
class Meetup extends Post
{

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255)
     */
    private $place;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime")
     */
    private $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime")
     */
    private $endAt;

    /**
     * @ORM\Column(name="nb_participant", type="smallint", nullable=true)
     */
   private $nbParticipant;

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Meetup
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     *
     * @return Meetup
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return Meetup
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }



    /**
     * Set nbParticipant
     *
     * @param integer $nbParticipant
     *
     * @return Meetup
     */
    public function setNbParticipant($nbParticipant)
    {
        $this->nbParticipant = $nbParticipant;

        return $this;
    }

    /**
     * Get nbParticipant
     *
     * @return integer
     */
    public function getNbParticipant()
    {
        return $this->nbParticipant;
    }
}

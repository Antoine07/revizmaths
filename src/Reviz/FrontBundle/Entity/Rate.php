<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rate
 *
 * @ORM\Table(name="rates")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\RateRepository")
 */
class Rate
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
     * @var int
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var int
     *
     * @ORM\Column(name="object_id", type="integer")
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=100)
     */
    private $entity;


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
     * Set score
     *
     * @param integer $score
     *
     * @return Rate
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return Rate
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return Rate
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
}

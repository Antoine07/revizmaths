<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Field
 *
 * @ORM\Table(name="fields")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\FieldRepository")
 */
class Field
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="Reviz\FrontBundle\Entity\Form", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $form;

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
     * @return Field
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
     * Set type
     *
     * @param string $type
     *
     * @return Field
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Field
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set form
     *
     * @param \Reviz\FrontBundle\Entity\Form $form
     *
     * @return Field
     */
    public function setForm(\Reviz\FrontBundle\Entity\Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \Reviz\FrontBundle\Entity\Form
     */
    public function getForm()
    {
        return $this->form;
    }
}

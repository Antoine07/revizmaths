<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Form
 *
 * @ORM\Table(name="forms")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\FormRepository")
 */
class Form
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
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\Column( type="string", length=100, nullable=false)
     */
    private $action;

    /**
     * @ORM\OneToMany(targetEntity="Reviz\FrontBundle\Entity\Field", mappedBy="form", cascade={"persist", "remove"})
     */
    private $fields;

    /**
     * @ORM\ManyToOne(targetEntity="Reviz\FrontBundle\Entity\Post")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @ORM\Column( type="boolean", nullable=false)
     */
    private $qcmOk = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Form
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Form
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Add field
     *
     * @param \Reviz\FrontBundle\Entity\Field $field
     *
     * @return Form
     */
    public function addField(\Reviz\FrontBundle\Entity\Field $field)
    {
        $this->fields[] = $field;

        $field->setForm($this);

        return $this;
    }

    /**
     * Remove field
     *
     * @param \Reviz\FrontBundle\Entity\Field $field
     */
    public function removeField(\Reviz\FrontBundle\Entity\Field $field)
    {
        $this->fields->removeElement($field);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Form
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
     * Set action
     *
     * @param string $action
     *
     * @return Form
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Set post
     *
     * @param \Reviz\FrontBundle\Entity\Post $post
     *
     * @return Form
     */
    public function setPost(\Reviz\FrontBundle\Entity\Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Reviz\FrontBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set qcmOk
     *
     * @param boolean $qcmOk
     *
     * @return Form
     */
    public function setQcmOk($qcmOk)
    {
        $this->qcmOk = $qcmOk;

        return $this;
    }

    /**
     * Get qcmOk
     *
     * @return boolean
     */
    public function getQcmOk()
    {
        return $this->qcmOk;
    }
}

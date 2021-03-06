<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\UserRepository")
 */
class User extends BaseUser
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\OneToOne(targetEntity="Reviz\FrontBundle\Entity\Userdata", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $userdata;

    /**
     * @var string
     *
     * @ORM\Column(type="smallint")
     */
    protected $nbModule = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="smallint")
     */
    protected $nbFormule = 0;


    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myStudents", cascade={"persist"})
     */
    protected $myStudents;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="profsWithMe", cascade={"persist"})
     * @ORM\JoinTable(name="profs",
     *      joinColumns={@ORM\JoinColumn(name="student_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="prof_id", referencedColumnName="id")}
     *      )
     */

    protected $myProfs;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->profsWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myProfs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set media
     *
     * @param \Reviz\FrontBundle\Entity\Media $media
     *
     * @return User
     */
    public function setMedia(\Reviz\FrontBundle\Entity\Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Reviz\FrontBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set userdata
     *
     * @param \Reviz\FrontBundle\Entity\Userdata $userdata
     *
     * @return User
     */
    public function setUserdata(\Reviz\FrontBundle\Entity\Userdata $userdata = null)
    {
        $this->userdata = $userdata;

        return $this;
    }

    /**
     * Get userdata
     *
     * @return \Reviz\FrontBundle\Entity\Userdata
     */
    public function getUserdata()
    {
        return $this->userdata;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nbModule
     *
     * @param integer $nbModule
     *
     * @return User
     */
    public function setNbModule($nbModule)
    {
        $this->nbModule = $nbModule;

        return $this;
    }

    /**
     * Get nbModule
     *
     * @return integer
     */
    public function getNbModule()
    {
        return $this->nbModule;
    }

    /**
     * Set nbFormule
     *
     * @param integer $nbFormule
     *
     * @return User
     */
    public function setNbFormule($nbFormule)
    {
        $this->nbFormule = $nbFormule;

        return $this;
    }

    /**
     * Get nbFormule
     *
     * @return integer
     */
    public function getNbFormule()
    {
        return $this->nbFormule;
    }


    /**
     * Add myStudent
     *
     * @param \Reviz\FrontBundle\Entity\User $myStudent
     *
     * @return User
     */
    public function addMyStudent(\Reviz\FrontBundle\Entity\User $myStudent)
    {
        $this->myStudents[] = $myStudent;

        return $this;
    }

    /**
     * Remove myStudent
     *
     * @param \Reviz\FrontBundle\Entity\User $myStudent
     */
    public function removeMyStudent(\Reviz\FrontBundle\Entity\User $myStudent)
    {
        $this->myStudents->removeElement($myStudent);
    }

    /**
     * Get myStudents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMyStudents()
    {
        return $this->myStudents;
    }

    /**
     * Add myProf
     *
     * @param \Reviz\FrontBundle\Entity\User $myProf
     *
     * @return User
     */
    public function addMyProf(\Reviz\FrontBundle\Entity\User $myProf)
    {
        $this->myProfs[] = $myProf;

        return $this;
    }

    /**
     * Remove myProf
     *
     * @param \Reviz\FrontBundle\Entity\User $myProf
     */
    public function removeMyProf(\Reviz\FrontBundle\Entity\User $myProf)
    {
        $this->myProfs->removeElement($myProf);
    }

    /**
     * Get myProfs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMyProfs()
    {
        return $this->myProfs;
    }

}

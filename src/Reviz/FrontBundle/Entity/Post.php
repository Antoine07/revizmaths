<?php

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="customType", type="string")
 * @ORM\DiscriminatorMap({"post" = "Post", "exercice" = "Exercice", "answer" = "Answer", "method" = "Method", "course" = "Course", "question"="Question", "meetup"="Meetup"})
 * @ORM\Entity(repositoryClass="Reviz\FrontBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
abstract class Post
{

    use StatusTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="post_parent", type="integer", nullable=true)
     */
    private $postParent;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\Length(min=5)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true))
     * @Assert\Type("\DateTime")
     */
    private $publishedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false))
     * @Assert\Type("\DateTime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Reviz\FrontBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Reviz\FrontBundle\Entity\Taxonomy",  inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $taxonomies;

    /**
     * @ORM\OneToMany(targetEntity="Reviz\FrontBundle\Entity\Comment", mappedBy="post", cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="Reviz\FrontBundle\Entity\Media", inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $medias;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxonomies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();

        $this->setStatus('unpublished');

        $this->setCreatedAt(new \DateTime());

    }

    /**
     * Add taxonomy
     *
     * @param \Reviz\FrontBundle\Entity\Taxonomy $taxonomy
     *
     * @return Post
     */
    public function addTaxonomy(\Reviz\FrontBundle\Entity\Taxonomy $taxonomy)
    {
        $this->taxonomies[] = $taxonomy;

        $taxonomy->addPost($this);

        $termName = get_class($taxonomy);

        $termName = substr($termName, strripos( $termName, '\\')+1);

        if (in_array($termName, ['Module', 'Category'])) {

            $className = get_class($this);
            $customType = substr($className, strripos( $className, '\\')+1);

            $setName = 'setNb' . ucfirst($customType);
            $getName = 'getNb' . ucfirst($customType);

            $taxonomy->$setName($taxonomy->$getName() + 1);
        }

        return $this;
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
        $className = get_class($this);
        $customType = substr($className, strripos( $className, '\\')+1);

        $decrease = 'decrease'.ucfirst($customType);

        foreach($this->getTaxonomies() as $taxonomy)
            $taxonomy->$decrease;
    }

    /**
     * Remove taxonomy
     *
     * @param \Reviz\FrontBundle\Entity\Taxonomy $taxonomy
     */
    public function removeTaxonomy(\Reviz\FrontBundle\Entity\Taxonomy $taxonomy)
    {
        $this->taxonomies->removeElement($taxonomy);

    }

    /**
     * Get taxonomies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxonomies()
    {
        return $this->taxonomies;
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Post
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set postParent
     *
     * @param integer $postParent
     *
     * @return Post
     */
    public function setPostParent($postParent)
    {
        $this->postParent = $postParent;

        return $this;
    }

    /**
     * Get postParent
     *
     * @return int
     */
    public function getPostParent()
    {
        return $this->postParent;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
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
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return Post
     *
     * @ORM\PrePersist
     */
    public function setPublishedAt()
    {
        $this->publishedAt = new \DateTime();

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set user
     *
     * @param \Reviz\FrontBundle\Entity\User $user
     *
     * @return Post
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


    /**
     * Add comment
     *
     * @param \Reviz\FrontBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\Reviz\FrontBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        // link comment to post
        $comment->setPost($this);

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Reviz\FrontBundle\Entity\Comment $comment
     */
    public function removeComment(\Reviz\FrontBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * Add image
     *
     * @param \Reviz\FrontBundle\Entity\Image $image
     *
     * @return Post
     */
    public function addImage(\Reviz\FrontBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Reviz\FrontBundle\Entity\Image $image
     */
    public function removeImage(\Reviz\FrontBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }


    /**
     * Add media
     *
     * @param \Reviz\FrontBundle\Entity\Media $media
     *
     * @return Post
     */
    public function addMedia(\Reviz\FrontBundle\Entity\Media $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * Remove media
     *
     * @param \Reviz\FrontBundle\Entity\Media $media
     */
    public function removeMedia(\Reviz\FrontBundle\Entity\Media $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedias()
    {
        return $this->medias;
    }
}

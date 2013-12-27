<?php
namespace Movies\MovieBundle\Entity;

use Serie\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Movies\MovieBundle\Entity\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment {
	
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $subject
     *
     * @ORM\Column(name="subject", type="string")
     */
    protected $subject;

    /**
     * @var text $message
     *
     * @ORM\Column(name="message", type="text")
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="Movies\AdminBundle\Entity\User", inversedBy="comments")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="comments")
     */
    protected $movie;

    /**
     * @ORM\ManyToOne(targetEntity="Actor", inversedBy="comments")
     */
    protected $actor;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="referenceComment")
     */
    protected $answers;

    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="answers")
     */
    protected $referenceComment;

    /**
     * @var date $dateComment
     *
     * @ORM\Column(name="dateComment", type="datetime")
     */
    protected $dateComment;


    public function getDateCommentToString()
    {
        $now = new \DateTime('now');
        $diff = $this->dateComment->diff($now);
        if ($diff->y == 1)
            return $diff->y . ' year ago';
        if ($diff->y > 1)
            return $diff->y . ' years ago';
        if ($diff->m == 1)
            return $diff->m . ' month ago';
        if ($diff->m > 1)
            return $diff->m . ' months ago';
        if ($diff->d == 1)
            return $diff->d . ' day ago';
        if ($diff->d > 1)
            return $diff->d . ' days ago';
        if ($diff->h == 1)
            return $diff->h . ' hour ago';
        if ($diff->h > 1)
            return $diff->h . ' hours ago';
        if ($diff->i == 1)
            return $diff->i . ' minute ago';
        if ($diff->i > 1)
            return $diff->i . ' minutes ago';
        if ($diff->s == 1)
            return $diff->s . ' second ago';
        if ($diff->s > 1)
            return $diff->s . ' seconds ago';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Comment
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Comment
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set dateComment
     *
     * @param \DateTime $dateComment
     * @return Comment
     */
    public function setDateComment($dateComment)
    {
        $this->dateComment = $dateComment;
    
        return $this;
    }

    /**
     * Get dateComment
     *
     * @return \DateTime 
     */
    public function getDateComment()
    {
        return $this->dateComment;
    }

    /**
     * Set user
     *
     * @param \Movies\AdminBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Movies\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Movies\AdminBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set serie
     *
     * @param \Movies\MovieBundle\Entity\Movie $serie
     * @return Comment
     */
    public function setSerie(\Movies\MovieBundle\Entity\Movie $serie = null)
    {
        $this->serie = $serie;
    
        return $this;
    }

    /**
     * Get serie
     *
     * @return \Movies\MovieBundle\Entity\Movie 
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set actor
     *
     * @param \Movies\MovieBundle\Entity\Actor $actor
     * @return Comment
     */
    public function setActor(\Movies\MovieBundle\Entity\Actor $actor = null)
    {
        $this->actor = $actor;
    
        return $this;
    }

    /**
     * Get actor
     *
     * @return \Movies\MovieBundle\Entity\Actor 
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * Add answers
     *
     * @param \Movies\MovieBundle\Entity\Comment $answers
     * @return Comment
     */
    public function addAnswer(\Movies\MovieBundle\Entity\Comment $answers)
    {
        $this->answers[] = $answers;
    
        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Movies\MovieBundle\Entity\Comment $answers
     */
    public function removeAnswer(\Movies\MovieBundle\Entity\Comment $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set referenceComment
     *
     * @param \Movies\MovieBundle\Entity\Comment $referenceComment
     * @return Comment
     */
    public function setReferenceComment(\Movies\MovieBundle\Entity\Comment $referenceComment = null)
    {
        $this->referenceComment = $referenceComment;
    
        return $this;
    }

    /**
     * Get referenceComment
     *
     * @return \Movies\MovieBundle\Entity\Comment 
     */
    public function getReferenceComment()
    {
        return $this->referenceComment;
    }

    /**
     * Set movie
     *
     * @param \Movies\MovieBundle\Entity\Movie $movie
     * @return Comment
     */
    public function setMovie(\Movies\MovieBundle\Entity\Movie $movie = null)
    {
        $this->movie = $movie;
    
        return $this;
    }

    /**
     * Get movie
     *
     * @return \Movies\MovieBundle\Entity\Movie 
     */
    public function getMovie()
    {
        return $this->movie;
    }
}
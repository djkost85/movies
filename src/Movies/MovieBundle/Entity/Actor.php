<?php
namespace Movies\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="actor")
 * @ORM\Entity(repositoryClass="Movies\MovieBundle\Entity\Repository\ActorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Actor {
	
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string")
     **/
    protected $name;

    /**
     * @var string $alias
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="alias", type="string", unique=true)
     */
    protected $alias;

    /**
     * @var string $feature
     *
     * @ORM\Column(name="feature", type="string", nullable=true)
     */
    protected $feature;

    /**
     * @var date $birthdayDate
     *
     * @ORM\Column(name="birthdayDate", type="date", nullable=true)
     */
    protected $birthdayDate;

    /**
     * @var string $resume
     *
     * @ORM\Column(name="resume", type="text", nullable=true)
     */
    protected $resume;

    /**
     * @var $movies
     *
     * @ORM\ManyToMany(targetEntity="Movie", mappedBy="cast", cascade={"persist"})
     **/
    protected $movies;

    /**
     * @var array $comments
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="actor")
     */
    protected $comments;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    private $temp;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->feature)) {
            // store the old name to delete after the update
            $this->temp = $this->feature;
            $this->feature = null;
        } else {
            $this->feature = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // do whatever you want to generate a unique name
            $filename = $this->getAlias();
            $this->feature = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->feature);

        // check if we have an old image
        if (isset($this->temp) && $this->temp != '' && $this->temp != $this->feature) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }


    public function getAbsolutePath()
    {
        return null === $this->feature
            ? null
            : $this->getUploadRootDir().'/'.$this->feature;
    }

    public function getWebPath()
    {
        return null === $this->feature
            ? null
            : $this->getUploadDir().'/'.$this->feature;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/actors';
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Actor
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
     * Set alias
     *
     * @param string $alias
     * @return Actor
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    
        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set feature
     *
     * @param string $feature
     * @return Actor
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    
        return $this;
    }

    /**
     * Get feature
     *
     * @return string 
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set birthdayDate
     *
     * @param \DateTime $birthdayDate
     * @return Actor
     */
    public function setBirthdayDate($birthdayDate)
    {
        $this->birthdayDate = $birthdayDate;
    
        return $this;
    }

    /**
     * Get birthdayDate
     *
     * @return \DateTime 
     */
    public function getBirthdayDate()
    {
        return $this->birthdayDate;
    }

    /**
     * Set resume
     *
     * @param string $resume
     * @return Actor
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
    
        return $this;
    }

    /**
     * Get resume
     *
     * @return string 
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * Add movies
     *
     * @param \Movies\MovieBundle\Entity\Movie $movies
     * @return Actor
     */
    public function addMovie(\Movies\MovieBundle\Entity\Movie $movies)
    {
        $this->movies[] = $movies;
    
        return $this;
    }

    /**
     * Remove movies
     *
     * @param \Movies\MovieBundle\Entity\Movie $movies
     */
    public function removeMovie(\Movies\MovieBundle\Entity\Movie $movies)
    {
        $this->movies->removeElement($movies);
    }

    /**
     * Get movies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * Add comments
     *
     * @param \Movies\MovieBundle\Entity\Comment $comments
     * @return Actor
     */
    public function addComment(\Movies\MovieBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Movies\MovieBundle\Entity\Comment $comments
     */
    public function removeComment(\Movies\MovieBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
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

    public function getNbFilms()
    {
        return count($this->movies);
    }

    public function getColumns()
    {
        $i=0;             
        $columns[$i]['name'] = 'Name';
        $columns[$i]['entity'] = 'name';
        $i++;
        $columns[$i]['name'] = 'Birthday'; 
        $columns[$i]['entity'] = 'birthdayDate'; 
        $i++;
        $columns[$i]['name'] = 'Preview';             
        $columns[$i]['entity'] = 'linkAdmin';
        return $columns; 
    }
}
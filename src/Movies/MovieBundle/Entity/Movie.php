<?php
namespace Movies\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="Movies\MovieBundle\Entity\Repository\MovieRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Movie
{

	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $title
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=200)
     */
    protected $title;

    /**
     * @var string $alias
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="alias", type="string", unique=true)
     */
    protected $alias;

    /**
     * @var text $resume
     * @Assert\NotBlank()
     * @ORM\Column(name="resume", type="text")
     */
	protected $resume;

	/**
	 * @var int duration
	 * @Assert\NotBlank()
	 * @Assert\Type(type="integer", message="La durée {{ value }} doit être un {{ type }}")
	 * @ORM\Column(name="duration", type="integer", length=3)
	 */
	protected $duration;

	/**
	 * @var int $year
	 * @Assert\NotBlank()
	 * @Assert\Type(type="integer", message="L'année {{ value }} doit être un {{ type }}")
	 * @Assert\Length(min="4", max="4", minMessage="L'année doit comporter 4 chiffres", maxMessage="L'année doit comporter 4 chiffres")
	 * @ORM\Column(name="year", type="integer", length=4)
	 */
	protected $year;

	/**
	 * @var String $quality
	 * @Assert\NotBlank()
	 * @ORM\Column(name="quality", type="string", length=50)
	 */
	protected $quality;

	/**
	 * @var int $note
	 * @Assert\Type(type="integer", message="La note {{ value }} doit être un {{ type }}")
	 * @Assert\Length(min="4", max="4", minMessage="La note doit comporter 1 chiffre", maxMessage="La note doit comporter 1 chiffre")
	 * @Assert\Range(min="0", max="5", minMessage="La note doit être comprise entre 0 et 5", maxMessage="La note doit être comprise entre 0 et 5")
	 * @ORM\Column(name="note", type="integer", length=1)
	 */
	protected $note;

	/**
	 * @var collection $cast
	 * @ORM\ManyToMany(targetEntity="Actor", inversedBy="movies")
	 */
	protected $cast;

	/**
	 * @var collection $genres
	 * @ORM\ManyToMany(targetEntity="Genre", inversedBy="movies")
	 */
	protected $genres;

	/**
	 * @var collection $language
     * 
	 * @ORM\ManyToOne(targetEntity="Language", inversedBy="movies")
	 */
	protected $language;

    /**
     * @var int $movies
     *
     * @ORM\ManyToMany(targetEntity="Movies\AdminBundle\Entity\User", mappedBy="movies")
     */
    protected $users;

    /**
     * @var array $comments
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="movie")
     */
    protected $comments;

    /**
     * @ORM\Column(name="added", type="datetime")
     */
    protected $added;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

     /**
     * @var string $feature
     *
     * @ORM\Column(name="feature", type="string", nullable=true)
     */
    protected $feature;

     /**
     * @var array $feature
     *
     * @ORM\Column(name="images", type="array", nullable=true)
     */
    protected $images;



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
        return 'uploads/movies';
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cast = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->language = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Movie
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
     * Set alias
     *
     * @param string $alias
     * @return Movie
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
     * Set resume
     *
     * @param string $resume
     * @return Movie
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
     * Set duration
     *
     * @param integer $duration
     * @return Movie
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    
        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set quality
     *
     * @param string $quality
     * @return Movie
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
    
        return $this;
    }

    /**
     * Get quality
     *
     * @return string 
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set note
     *
     * @param integer $note
     * @return Movie
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return integer 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Add cast
     *
     * @param \Movies\MovieBundle\Entity\Actor $cast
     * @return Movie
     */
    public function addCast(\Movies\MovieBundle\Entity\Actor $cast)
    {
        $this->cast[] = $cast;
    
        return $this;
    }

    /**
     * Remove cast
     *
     * @param \Movies\MovieBundle\Entity\Actor $cast
     */
    public function removeCast(\Movies\MovieBundle\Entity\Actor $cast)
    {
        $this->cast->removeElement($cast);
    }

    /**
     * Get cast
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * Add genres
     *
     * @param \Movies\MovieBundle\Entity\Genre $genres
     * @return Movie
     */
    public function addGenre(\Movies\MovieBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;
    
        return $this;
    }

    /**
     * Remove genres
     *
     * @param \Movies\MovieBundle\Entity\Genre $genres
     */
    public function removeGenre(\Movies\MovieBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add language
     *
     * @param \Movies\MovieBundle\Entity\Language $language
     * @return Movie
     */
    public function addLanguage(\Movies\MovieBundle\Entity\Language $language)
    {
        $this->language[] = $language;
    
        return $this;
    }

    /**
     * Remove language
     *
     * @param \Movies\MovieBundle\Entity\Language $language
     */
    public function removeLanguage(\Movies\MovieBundle\Entity\Language $language)
    {
        $this->language->removeElement($language);
    }

    /**
     * Get language
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Add users
     *
     * @param \Movies\AdminBundle\Entity\User $users
     * @return Movie
     */
    public function addUser(\Movies\AdminBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Movies\AdminBundle\Entity\User $users
     */
    public function removeUser(\Movies\AdminBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add comments
     *
     * @param \Movies\MovieBundle\Entity\Comment $comments
     * @return Movie
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

    /**
     * Set language
     *
     * @param \Movies\MovieBundle\Entity\Language $language
     * @return Movie
     */
    public function setLanguage(\Movies\MovieBundle\Entity\Language $language = null)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     * @return Movie
     */
    public function setAdded($added)
    {
        $this->added = $added;
    
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setAddedValue()
    {
        $this->added = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Get added
     *
     * @return \DateTime 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Movie
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function getLinkAdmin()
    {
        return '<a href="/movies/'.$this->alias.'" target="_blank">Preview</a>';
    }

    public function getColumns()
    {
        $i=0;             
        $columns[$i]['name'] = 'Title';
        $columns[$i]['entity'] = 'title';
        $i++;
        $columns[$i]['name'] = 'Genres'; 
        $columns[$i]['entity'] = 'genresToString'; 
        $i++;
        $columns[$i]['name'] = 'Duration'; 
        $columns[$i]['entity'] = 'duration';
        /*$i++;
        $columns[$i]['name'] = 'Rating';              
        $columns[$i]['entity'] = 'rating';*/
        $i++;
        $columns[$i]['name'] = 'Preview';             
        $columns[$i]['entity'] = 'linkAdmin';
        return $columns; 
    }

    public function genresToString()
    {
        $str = array();
        foreach($this->genres as $genre) {
            $str[] = $genre->getTitle();
        }
        return implode(', ', $str);
    }

    /**
     * Set feature
     *
     * @param string $feature
     * @return Movie
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
     * Set images
     *
     * @param array $images
     * @return Movie
     */
    public function setImages($images)
    {
        $this->images = $images;
    
        return $this;
    }

    /**
     * Get images
     *
     * @return array 
     */
    public function getImages()
    {
        return $this->images;
    }
}
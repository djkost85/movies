<?php
namespace Movies\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="Movies\MovieBundle\Entity\Repository\GenreRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Genre {
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string")
     */
    protected $title;

    /**
     * @var string $alias
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="alias", type="string", unique=true)
     */
    protected $alias;

     /**
     * @var $movies
     * 
     * @ORM\ManyToMany(targetEntity="Movie", mappedBy="genres", cascade={"persist"})
     **/
    protected $movies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getUrl() {
        return '<a href="/genres/'.$this->alias.'>Preview</a>';
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
     * @return Genre
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
     * @return Genre
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
     * Add movies
     *
     * @param \Movies\MovieBundle\Entity\Movie $movies
     * @return Genre
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
}
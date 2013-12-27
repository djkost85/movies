<?php
namespace Movies\AdminBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

use Movies\MovieBundle\Entity\Movie;
use Movies\MovieBundle\Entity\Comment;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Movies\AdminBundle\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40, nullable=false)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string", nullable=false)
     * @Assert\NotBlank()
     **/
    protected $firstname;

    /**
     * @var string $lastname
     *
     * @ORM\Column(name="lastname", type="string", nullable=false)
     * @Assert\NotBlank()
     **/
    protected $lastname;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     **/
    protected $avatar;

    /**
     * @var array $roles
     *
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="users")
     */
    protected $roles;

    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\Image()
     */
    private $file;

    /**
     * @var \DateTime $lastLogin
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    protected $lastLogin;

    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * @var \DateTime $added
     *
     * @ORM\Column(name="added", type="datetime")
     * @Assert\Datetime()
     */
    protected $added;

    /**
     * @var int $movies
     *
     * @ORM\ManyToMany(targetEntity="Movies\MovieBundle\Entity\Movie", inversedBy="users")
     * @ORM\JoinTable(name="users_movies")
     */
    protected $movies;

    /**
     * @var array $comments
     *
     * @ORM\OneToMany(targetEntity="Movies\MovieBundle\Entity\Comment", mappedBy="user")
     */
    protected $comments;

    /**
     * @var string $website
     *
     * @ORM\Column(name="website", type="string", nullable=true)
     * @Assert\Url()
     */
    protected $website;


    private $temp;

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->avatar)) {
            // store the old name to delete after the update
            $this->temp = $this->avatar;
            $this->avatar = null;
        } else {
            $this->avatar = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->avatar = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->avatar);

        // check if we have an old image
        if (isset($this->temp)) {
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
    	return null === $this->avatar
            ? null
            : $this->getUploadRootDir().'/'.$this->avatar;
    }

    public function getWebPath()
    {
        return null === $this->avatar
            ? null
            : $this->getUploadDir().'/'.$this->avatar;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/users';
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
     * Set username
     *
     * @param string $username
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
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->getWebPath();
    }

    public function getRoles()
    {
        $roles = array();
        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    public function getRolesCollection()
    {
        return $this->roles;
    }

    public function getRolesToString() {
        $roles = array();
        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }
        return implode(', ', $roles);
    }


    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }


    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function equals(UserInterface $user)
    {
        return $this->username === $user->getUsername();
    }
    
    /**
     * Serializes the content of the current User object
     * @return string
     */
    public function serialize()
    {
        return \json_encode(
                array($this->username, $this->password, $this->salt,
                         $this->id));
    }

    /**
     * Unserializes the given string in the current User object
     * @param serialized
     */
    public function unserialize($serialized)
    {
        list($this->username, $this->password, $this->salt,
                         $this->id) = \json_decode(
                $serialized);
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->roles = new ArrayCollection();
    }
    
    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
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
     * Set lastLogin
     *
     * @param \Datetime $lastLogin
     * @return User
     */
    public function setLastLogin(\Datetime $lastLogin)
    {
        $this->lastLogin = $lastLogin;
    
        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \Datetime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set added
     *
     * @param \Datetime $added
     * @return User
     */
    public function setAdded(\Datetime $added)
    {
        $this->added = $added;
    
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setAddedValue() 
    {
        $this->added = new \DateTime('now');
    }   

    /**
     * Get added
     *
     * @return \Datetime 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Add roles
     *
     * @param \Movies\AdminBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Movies\AdminBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Movies\AdminBundle\Entity\Role $roles
     */
    public function removeRole(\Movies\AdminBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }


    public function getEnabledToString()
    {
        if($this->enabled)
            return '<i class="glyphicon glyphicon-ok"></i>';
        return '<i class="glyphicon glyphicon-remove"></i>';
    }

    /**
     * Get series
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeries()
    {
        return $this->series;
    }

    public function getAvatarImage() 
    {
        return $this->getWebPath();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getNiceName()
    {
        return $this->firstname.' '.$this->lastname;
    }



    /**
     * Add comments
     *
     * @param \Movies\MovieBundle\Entity\Comment $comments
     * @return User
     */
    public function addComment(Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Movies\MovieBundle\Entity\Comment $comments
     */
    public function removeComment(Comment $comments)
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
     * Set website
     *
     * @param string $website
     * @return User
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Add movies
     *
     * @param \Movies\MovieBundle\Entity\Movie $movies
     * @return User
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

    public function getDisplayName() {
        return $this->username;
    }
}
<?php

namespace JoranBeaufort\Neo4jUserBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use JoranBeaufort\Neo4jUserBundle\Entity\UserRole;
use JoranBeaufort\Neo4jUserBundle\Entity\Role;
use AppBundle\Entity\UserResource;
use AppBundle\Entity\Resources;
use AppBundle\Entity\UserTeam;
use AppBundle\Entity\Team;
use AppBundle\Entity\UserTile;
use AppBundle\Entity\Tile;
use AppBundle\Entity\UserTileLost;
use AppBundle\Entity\TileLost;
use AppBundle\Entity\UserInventory;
use AppBundle\Entity\Inventory;


/**
 * @OGM\Node(label="User")
 */
 
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    private $id;

    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    private $uint;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $uid;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
    private $emailCanonical;

    /**
     * @OGM\Property(type="string")
     * @var string
     * @Assert\Regex("/^[\w\d\s.,-]*$/")
     * @Assert\NotBlank()
     */
    private $username;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
    private $usernameCanonical;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     * @Assert\Regex("/^[\w\d\s.,-]*$/")
     * @Assert\NotBlank()
     */
    private $screenname;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $gender;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $birthdate;

    /**
     * @Assert\Length(min = 4, max=4096)
     */
    private $plainPassword;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $password;    
    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $registrationDateTime;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
    private $profileImage;  
    
    /**
     * @Assert\Image(
     *     maxSize = "300k",
     *     minWidth = 200,
     *     maxWidth = 400,
     *     minHeight = 200,
     *     maxHeight = 400
     * )
     */
    private $profileImageFile;    
    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
    private $profileDescription;

    /**
     * @OGM\Property(type="int")
     * @var int
     */

    private $xp;

    /**
     * @OGM\Property(type="int")
     * @var int
     */

    private $primary;

    /**
     * @OGM\Property(type="int")
     * @var int
     */

    private $secondary;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $confirmationToken;    
    
    
    /**
     * @OGM\Property(type="boolean")
     * @var boolean
     */
     
    private $isActive;    
    
    /**
     * @OGM\Property(type="boolean")
     * @var boolean
     */
     
    private $isAccountNonExpired;
    
    /**
     * @OGM\Property(type="boolean")
     * @var boolean
     */
     
    private $isAccountNonLocked;
    
    /**
     * @OGM\Property(type="boolean")
     * @var boolean
     */
     
    private $isCredentialsNonExpired;
    
    /**
     * @OGM\Property(type="boolean")
     * @var boolean
     */
     
    private $isEnabled;
    
    /**
     * @OGM\Relationship(relationshipEntity="\JoranBeaufort\Neo4jUserBundle\Entity\UserRole", type="HAS_ROLE", direction="OUTGOING", collection=true, mappedBy="user")
     * @OGM\Lazy()
     * @var ArrayCollection|\JoranBeaufort\Neo4jUserBundle\Entity\UserRole[]
     */
    protected $userRoles;
        
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserResource", type="HAS_RESOURCE", direction="OUTGOING", collection=true, mappedBy="user")
     * @var ArrayCollection|\AppBundle\Entity\UserResource[]
     */
    protected $userResources;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTeam", type="IN_TEAM", direction="OUTGOING", collection=true, mappedBy="user")
     * @OGM\Lazy()
     * @var  ArrayCollection|\AppBundle\Entity\UserTeam[]
     */
    protected $userTeam;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTile", type="CAPTURED", direction="OUTGOING", collection=true, mappedBy="user")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserTile[]
     */
    protected $userTiles;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTileLost", type="LOST", direction="OUTGOING", collection=true, mappedBy="user")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserTileLost[]
     */
    protected $userTilesLost;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserInventory", type="HAS_INVENTORY", direction="OUTGOING", collection=true, mappedBy="user")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserInventory[]
     */
    protected $userInventory;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->userRoles = new ArrayCollection();
        $this->userResources = new ArrayCollection();
        $this->userTeam = new ArrayCollection();
        $this->userTiles = new ArrayCollection();
        $this->userTilesLost = new ArrayCollection();
        $this->userInventory = new ArrayCollection();
    }

    // other properties and methods

    public function getId()
    {
        return $this->id;
    }

    public function getUint()
    {
        return $this->uint;
    }

    public function setUint($uint)
    {
        $this->uint = $uint;
        return $this;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
        return $this;
    }

    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    
    public function getScreenname()
    {
        return $this->screenname;
    }

    public function setScreenname($screenname)
    {
        $this->screenname = $screenname;
        return $this;
    }
    
    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }
    
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }    
    
    public function getOldPassword()
    {
        return $this->oldPassword;
    }
    
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }    
    
    public function getNewPassword()
    {
        return $this->newPassword;
    }
    
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }    
    
    public function getRegistrationDateTime()
    {
        return $this->registrationDateTime;
    }
    
    public function setRegistrationDateTime($registrationDateTime)
    {
        $this->registrationDateTime = $registrationDateTime;
    }    
    
    public function getProfileImage()
    {
        return $this->profileImage;
    }
    
    public function setProfileImage($profileImage)
    {
        $this->profileImage = $profileImage;
    }    
    
    public function getProfileImageFile()
    {
        return $this->profileImageFile;
    }
    
    public function setProfileImageFile($profileImageFile)
    {
        $this->profileImageFile = $profileImageFile;
    }    
    
    public function getProfileDescription()
    {
        return $this->profileDescription;
    }
    
    public function setProfileDescription($profileDescription)
    {
        $this->profileDescription = $profileDescription;
    }

    public function getXP()
    {
        return $this->xp;
    }

    public function setXP($xp)
    {
        $this->xp = $xp;
    }


    public function addXP($amount)
    {
        $xp = $this->xp;
        $this->xp = $xp+$amount;
        return $this;
    }

    public function getLvl()
    {
        $lvl = bcdiv(sqrt($this->xp),1,0);
        return $lvl;
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function setPrimary($primary)
    {
        $this->primary = $primary;
    }

    public function getSecondary()
    {
        return $this->secondary;
    }

    public function setSecondary($secondary)
    {
        $this->secondary = $secondary;
    }


    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }   

    public function setIsAccountNonExpired($isAccountNonExpired)
    {
        $this->isAccountNonExpired = $isAccountNonExpired;
    }
    
    public function isAccountNonExpired()
    {
        return $this->isAccountNonExpired;
    }
    
    public function setIsAccountNonLocked($isAccountNonLocked)
    {
        $this->isAccountNonLocked = $isAccountNonLocked;
    }


    public function isAccountNonLocked()
    {
        return $this->isAccountNonLocked;
    }

    public function setIsCredentialsNonExpired($isCredentialsNonExpired)
    {
        $this->isCredentialsNonExpired = $isCredentialsNonExpired;
    }

    public function isCredentialsNonExpired()
    {
        return $this->isCredentialsNonExpired;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }
    
    public function isEnabled()
    {
        return $this->isEnabled;
    }
    

    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\JoranBeaufort\Neo4jUserBundle\Entity\UserRole[]
     */
    public function getRoles()
    {
        $roles = array();
        foreach($this->userRoles as $userRole){
            array_push($roles,$userRole->getRole()->getRoleType());
        }
        return $roles;
        return $this;
    }        
    
    
    /**
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\Role $role
     */
    public function addRole(Role $role)
    {

        $ur = new UserRole($this, $role);
        $this->userRoles->add($ur);
        $role->addUserRole($ur);
        return $this;
        
    }


    /**
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\Role $role
     */
    public function removeRole(Role $role)
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\UserResource[]
     */
    public function getUserResources()
    {        
        return $this->userResources;
    }
    
    /**
     * @return \AppBundle\Entity\UserResource
     * @param string $name
     */
    public function getUserResource($name)
    {

        foreach($this->userResources as $resource){
            if($resource->getResource()->getName() == $name){
                return $resource;
            }
        }
        
    }    
     
    
    /**
     * @return \AppBundle\Entity\UserResource
     * @param int $id
     */
    public function getUserResourceById($id)
    {

        foreach($this->userResources as $resource){
            if($resource->getResource()->getId() == $id){
                return $resource;                
            }
        }        
    }
    

    /** 
     * @param \AppBundle\Entity\Resources $resources
     * @param int $amount
     */
    public function addResource(Resources $resources, $amount)
    {
        foreach ( $this->userResources as $resource) {
            if ($resource->getResource()->getName() === $resources->getName()) {
                return;
            }
        }

        $ur = new UserResource($this, $resources, $amount);
        $this->userResources->add($ur);
        $resources->addUserResources($ur); 
        return $this;
    }
    
    /**
     * @return \AppBundle\Entity\UserTeam
     */   
    public function getUserTeam()
    {        
        return $this->userTeam->first();
    }
    
    /**
     * @param \AppBundle\Entity\Team $team
     * @param int $joined
     */
    public function addTeam(Team $team, $joined)
    {

        $ut = new UserTeam($this, $team, $joined);
        $this->userTeam->add($ut);
        $team->addUserTeam($ut);
        return $this;
        
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\UserTile[]
     */
    public function getUserTiles()
    {        
        return $this->userTiles;
    }
    
    
    /**
     * @param \AppBundle\Entity\Tile $tile
     * @param int $captured
     * @param int $collected
     */
    public function addUserTile(Tile $tile, $captured, $collected, $landcover)
    {
            $ut = new UserTile($this, $tile, $captured, $collected, $landcover);
            $this->userTiles->add($ut);
            $tile->setUserTile($ut);
            return $this;
    }

    /**
     * @param \AppBundle\Entity\Tile $tile
     */
    public function removeUserTile(Tile $tile)
    {
        $userTile = $tile->getUserTile();
        $this->userTiles->removeElement($userTile);
        $tile->removeUserTile($userTile);
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\UserTileLost[]
     */

    public function getUserTilesLost()
    {
            return $this->userTilesLost;
    }

    /**
     * @param \AppBundle\Entity\Tile $tile
     * @param int $lost
     */
    public function addUserTileLost(Tile $tile, $lost)
    {

            $utl = new UserTileLost($this, $tile, $lost);
            $this->userTilesLost->add($utl);
            $tile->setUserTileLost($utl);
            return $this;
    }
    
    /**
     * @return \AppBundle\Entity\UserInventory
     */   
    public function getUserInventory()
    {        
        return $this->userInventory->first();
    }
    
    /**
     * @param \AppBundle\Entity\Inventory $inventory
     */
    public function addInventory(Inventory $inventory)
    {
        $ui = new UserInventory($this, $inventory);
        $this->userInventory->add($ui);
        $inventory->addUserInventory($ui);
        return $this;
        
    }


    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
    
    // other methods, including security methods like getRoles()
}

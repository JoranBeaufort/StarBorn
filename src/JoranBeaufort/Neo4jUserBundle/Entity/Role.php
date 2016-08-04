<?php
namespace JoranBeaufort\Neo4jUserBundle\Entity;

// Remember to create the role nodes in the neo4j graph
// create (r:Role{roleType:'ROLE_USER'})
// Add as many roles as needed.

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;


/**
 * @OGM\Node(label="Role")
 */
 
class Role
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    private $id;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    private $roleType;
    
    /**
     * @OGM\Relationship(relationshipEntity="\JoranBeaufort\Neo4jUserBundle\Entity\UserRole", type="HAS_ROLE", direction="INCOMING", collection=true, mappedBy="role")
     * @var \JoranBeaufort\Neo4jUserBundle\Entity\UserRole[]
     */
    protected $userRoles;
        
    
    
    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getRoleType()
    {
        return $this->roleType;
    }

    public function setRoleType($roleType)
    {
        $this->roleType = $roleType;
    }

    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\JoranBeaufort\Neo4jUserBundle\Entity\UserRole[]
     */
    public function getUsers()
    {
        return $this->userRoles;
    }

    /**
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\UserRole $userRole
     */
    public function addUserRole(UserRole $userRole)
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
        }
    }

    /**
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\UserRole $userRole
     */
    public function removeUserRole(UserRole $userRole)
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
        }
    }
}
<?php
namespace JoranBeaufort\Neo4jUserBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use JoranBeaufort\Neo4jUserBundle\Entity\Role;

/**
 * @OGM\RelationshipEntity(type="HAS_ROLE")
 */
 
class UserRole
{
    /**
     * @OGM\GraphId()
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\JoranBeaufort\Neo4jUserBundle\Entity\User")
     * @var \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    protected $user;

    /**
     * @OGM\EndNode(targetEntity="\JoranBeaufort\Neo4jUserBundle\Entity\Role")
     * @var \JoranBeaufort\Neo4jUserBundle\Entity\Role
     */
    protected $role;
    

    /**
     * @param User $user
     * @param Role $role
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }


    public function getId()
    {
        return $this->id;
    }    
    
    /**
     * @return \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \JoranBeaufort\Neo4jUserBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    
}
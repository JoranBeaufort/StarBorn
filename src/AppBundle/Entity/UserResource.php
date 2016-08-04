<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\Resources;

/**
 * @OGM\RelationshipEntity(type="HAS_RESOURCE")
 */
 
class UserResource
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\JoranBeaufort\Neo4jUserBundle\Entity\User")
     * @var \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    protected $user;

    /**
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Resources")
     * @var \AppBundle\Entity\Resources
     */
    protected $resources;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $amount;    


    /**
     * UserResource constructor.
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\User $user
     * @param \AppBundle\Entity\Resources $resources
     * @param int $amount
     */
    public function __construct(User $user, Resources $resources, $amount)
    {
        $this->user = $user;
        $this->resources = $resources;
        $this->amount = $amount;
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
     * @return \AppBundle\Entity\Resources
     */
    public function getResource()
    {
        return $this->resources;
    }

    /**
     * @return int
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }    
    
    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
        
    
}
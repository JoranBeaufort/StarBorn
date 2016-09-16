<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\Inventory;

/**
 * @OGM\RelationshipEntity(type="HAS_INVENTORY")
 */
 
class UserInventory
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
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Inventory")
     * @var \AppBundle\Entity\Inventory
     */
    protected $inventory;
    

    /**
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\User $user
     * @param \AppBundle\Entity\Inventory $inventory
     */
    public function __construct(User $user, Inventory $inventory)
    {
        $this->user = $user;
        $this->inventory = $inventory;
    }

        
    /**
     * @return \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \AppBundle\Entity\Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

}
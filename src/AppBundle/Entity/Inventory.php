<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Inventory")
 */
 
class Inventory
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $capacity;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserInventory", type="HAS_INVENTORY", direction="INCOMING", collection=true, mappedBy="inventory")
     * @var ArrayCollection|\AppBundle\Entity\UserInventory[]
     */
    protected $userInventory;
    
    public function __construct($capacity)
    {
        $this->capacity = $capacity;
        $this->userInventory = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }    
    
    public function getCapacity()
    {
        return $this->capacity;
    }    
    
    
    /**
     * @param \AppBundle\Entity\UserInventory $userInventory
     */
    public function addUserInventory(UserInventory $userInventory)
    {
        return $this->userInventory->add($userInventory);
    }
    
    /**
     * @return \AppBundle\Entity\UserInventory
     */
    public function getUserInventory()
    {
        return $this->userInventory;
    }
}
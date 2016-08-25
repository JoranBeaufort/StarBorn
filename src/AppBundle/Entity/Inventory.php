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
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserInventory[]
     */
    protected $userInventory;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\BlueprintInventory", type="CONTAINS", direction="OUTGOING", collection=true, mappedBy="inventory")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\BlueprintInventory[]
     */
     
    protected $blueprintInventory;    
        
    public function __construct($capacity)
    {
        $this->capacity = $capacity;
        $this->userInventory = new ArrayCollection();
        $this->blueprintInventory = new ArrayCollection();
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
        return $this->userInventory->first();
    }
    
    /**
     * @param \AppBundle\Entity\Blueprint $blueprint
     * @param int $amount
     */
    public function addBlueprintInventory(Blueprint $blueprint, $amount)
    {
        $bi = new BlueprintInventory($this, $blueprint, $amount);
        $this->blueprintInventory->add($bi);
        $blueprint->addBlueprintInventory($bi);
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\BlueprintInventory
     */
    public function getBlueprintInventory()
    {
        return $this->blueprintInventory;
    }
}
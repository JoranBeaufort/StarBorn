<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Drone;

/**
 * @OGM\RelationshipEntity(type="CONTAINS")
 */
 
class BlueprintInventory
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\AppBundle\Entity\Inventory")
     * @var \AppBundle\Entity\Inventory
     */
    protected $inventory;

    
    /**
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Blueprint")
     * @var \AppBundle\Entity\Blueprint
     */
    protected $blueprint;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $amount;   
        

    /**
     * UserResource constructor.
     * @param \AppBundle\Entity\Inventory $inventory
     * @param \AppBundle\Entity\Blueprint $blueprint
     * @param int $amount
     */
    public function __construct(Inventory $inventory, Blueprint $blueprint,  $amount)
    {
        $this->inventory = $inventory;
        $this->blueprint = $blueprint;
        $this->amount = $amount;
    }


    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \AppBundle\Entity\Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }
    
    /**
     * @return \AppBundle\Entity\Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

        
    /**
     * @var int $amount
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
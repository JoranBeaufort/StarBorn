<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Drone;

/**
 * @OGM\RelationshipEntity(type="COSTS")
 */
 
class BlueprintResources
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\AppBundle\Entity\Blueprint")
     * @var \AppBundle\Entity\Blueprint
     */
    protected $Blueprint;

    
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
     * @param \AppBundle\Entity\Blueprint $blueprint
     * @param \AppBundle\Entity\Resources $resources
     * @param int $amount
     */
    public function __construct(Blueprint $blueprint, Resources $resources,  $amount)
    {
        $this->blueprint = $blueprint;
        $this->resources = $resources;
        $this->amount = $amount;
    }


    public function getId()
    {
        return $this->id;
    }
    

    /**
     * @return \AppBundle\Entity\Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }
    
    /**
     * @return \AppBundle\Entity\Resources
     */
     
    public function getResources()
    {
        return $this->resources;
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
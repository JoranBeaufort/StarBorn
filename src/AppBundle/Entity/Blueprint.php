<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;


/**
 * @OGM\Node(label="Blueprint")
 */
 
class Blueprint
{
    /**
     * @OGM\GraphId()
     */
    protected $id;
        
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $bid;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $type;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name;

    /**
     * @OGM\Property(type="int")
     * @var int
     */

    protected $minlvl;

    /**
     * @OGM\Property(type="int")
     * @var int
     */

    protected $ilvl;
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name_DE;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $img;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\BlueprintResources", type="COSTS", direction="OUTGOING", collection=true, mappedBy="blueprint")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\BlueprintResources[]
     */
     
    protected $blueprintResources;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\BlueprintInventory", type="CONTAINS", direction="INCOMING", collection=true, mappedBy="blueprint")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\BlueprintInventory[]
     */
     
    protected $blueprintInventories;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\BlueprintStructure", type="BUILDS", direction="OUTGOING", collection=true, mappedBy="blueprint")
     * @var ArrayCollection|\AppBundle\Entity\BlueprintStructure[]
     */
     
    protected $blueprintStructure;    
        
     
    public function __construct()
    {
        $this->blueprintResources = new ArrayCollection();
        $this->blueprintInventories = new ArrayCollection();        
        $this->blueprintStructure = new ArrayCollection();        
    }

    
    public function getId()
    {
        return $this->id;
    }
    
    public function getBid()
    {
        return $this->bid;
    }
    
    public function getBlueprintType()
    {
        return $this->type;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getMinlvl()
    {
        return $this->minlvl;
    }

    public function getIlvl()
    {
        return $this->ilvl;
    }

    public function getName_DE()
    {
        return $this->name_DE;
    }
    
    public function getImg()
    {
        return $this->img;
    }
    
    /**
     * @return BlueprintResources|BlueprintResources[]|ArrayCollection
     */
    public function getBlueprintResources()
    {
        
        return $this->blueprintResources;
    }
    
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\BlueprintInventory
     */
    public function getBlueprintInventories()
    {
        return $this->blueprintInventories;
    }
    
    /**
     * @param \AppBundle\Entity\BlueprintInventory $blueprintInventory
     */
    public function addBlueprintInventory(BlueprintInventory $blueprintInventory)
    {
        $this->blueprintInventories->add($blueprintInventory);
        return $this;
    }
    
    /**
     * @param \AppBundle\Entity\BlueprintInventory $blueprintInventory
     */
    public function removeBlueprintInventory(BlueprintInventory $blueprintInventory)
    {
        $this->blueprintInventories->removeElement($blueprintInventory);
    }
    
    /**
     * @return \AppBundle\Entity\BlueprintStructure
     */
    public function getBlueprintStructure()
    {
        return $this->blueprintStructure->first();
    }
    
}
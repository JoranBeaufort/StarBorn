<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
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
     * @OGM\Lazy()
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
        return $this->tid;
    }
    
    public function getBlueprinttype()
    {
        return $this->type;
    }
    
    public function getName()
    {
        return $this->name;
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
     * @return \AppBundle\Entity\BlueprintResources
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
     * @var \AppBundle\Entity\BlueprintInventory $blueprintInventory
     */
    public function addBlueprintInventory($blueprintInventory)
    {
        $this->blueprintInventories->add($blueprintInventory);
        return $this;
    }
    
    /**
     * @var \AppBundle\Entity\BlueprintInventory $blueprintInventory
     */
    public function removeBlueprintInventory(UserTileLost $userTileLost)
    {
        $this->userTileLost->removeElement($userTileLost);
        return $this;
    }
    
    /**
     * @return \AppBundle\Entity\BlueprintStructure
     */
    public function getBlueprintStructure()
    {
        return $this->blueprintStructure->first();
    }
    
}
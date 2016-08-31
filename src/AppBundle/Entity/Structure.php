<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use AppBundle\Entity\Tile;
use AppBundle\Entity\TileStructure;
use AppBundle\Entity\BlueprintStructure;


/**
 * @OGM\Node(label="Structure")
 */
 
class Structure
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
     
    protected $sid;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $hp;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $img;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $type;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name_DE;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileStructure", type="HAS_STRUCTURE", direction="INCOMING", collection=true, mappedBy="structure")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileStructure[]
     */
     
    protected $tileStructures;  

    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\BlueprintStructure", type="BUILDS", direction="INCOMING", collection=true, mappedBy="structure")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\BlueprintStructure[]
     */
     
    protected $blueprintStructure;  
    
    
    public function __construct()
    {
        $this->tileStructures = new ArrayCollection();
        $this->blueprintStructure = new ArrayCollection();
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getSid()
    {
        return $this->sid;
    }
    
    public function getHp()
    {
        return $this->hp;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getImg()
    {
        return $this->img;
    }
    
    public function getStructureType()
    {
        return $this->type;
    }
    
    public function getName_DE()
    {
        return $this->name_DE;
    }
    
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\TileStructure[]
     */
    public function getTileStructures()
    {
        return $this->tileStructures;
    }
    
    /**
     * @var \AppBundle\Entity\TileStructure
     */
    public function addTileStructure($tileStructure)
    {
        $this->tileStructures->add($tileStructure);
    }
    
    /**
     * @var \AppBundle\Entity\TileStructure
     */
    public function removeTileStructure($tileStructure)
    {
        $this->tileStructures->removeElement($tileStructure);
    }
    
    /**
     * @return \AppBundle\Entity\BlueprintStructure
     */
    public function getBlueprintStructure()
    {
        return $this->blueprintStructure;
    }
    
}
<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;


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
     * @var ArrayCollection|\AppBundle\Entity\TileStructure[]
     */
     
    protected $tileStructures;  

    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\BlueprintStructure", type="BUILDS", direction="INCOMING", collection=true, mappedBy="structure")
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

    public function getIlvl()
    {
        $ilvl = $this->getBlueprintStructure()->getBlueprint()->getIlvl();
        return $ilvl;
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
     * @param \AppBundle\Entity\TileStructure $tileStructure
     */
    public function addTileStructure(TileStructure $tileStructure)
    {
        $this->tileStructures->add($tileStructure);
    }
    
    /**
     * @param \AppBundle\Entity\TileStructure $tileStructure
     */
    public function removeTileStructure(TileStructure $tileStructure)
    {
        $this->tileStructures->removeElement($tileStructure);
    }

    /**
     * @return \AppBundle\Entity\BlueprintStructure
     */

    public function getBlueprintStructure()
    {

        return $this->blueprintStructure->first();
    }
    
}
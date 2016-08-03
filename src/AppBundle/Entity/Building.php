<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use AppBundle\Entity\Tile;
use AppBundle\Entity\TileDrone;


/**
 * @OGM\Node(label="Building")
 */
 
class Building
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
     
    protected $bid;
    
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
     
    protected $name_DE;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileBuilding", type="HAS_BUILDING", direction="INCOMING", collection=true, mappedBy="building")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileBuilding
     */
     
    protected $tileBuilding;  
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getDid()
    {
        return $this->did;
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
    
    public function getName_DE()
    {
        return $this->name_DE;
    }
    
    
    /**
     * @return \AppBundle\Entity\TileBuilding
     */
    public function getTileBuilding()
    {
        return $this->tileBuilding;
    }
    
    /**
     * @var \AppBundle\Entity\TileBuilding
     */
    public function setTileBuilding($tileBuilding)
    {
        $this->tileBuilding->add($tileBuilding);
    }
    
    /**
     * @var \AppBundle\Entity\TileBuilding
     */
    public function removeTileBuilding($tileBuilding)
    {
        $this->tileBuilding->removeElement($tileBuilding);
    }
    
}
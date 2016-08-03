<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use AppBundle\Entity\Tile;
use AppBundle\Entity\TileShield;


/**
 * @OGM\Node(label="Shield")
 */
 
class Shield
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
     
    protected $name_DE;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileShield", type="HAS_SHIELD", direction="INCOMING", collection=false, mappedBy="shield")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileShield
     */
     
    protected $tileShield;  
    
    
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
    
    public function getName_DE()
    {
        return $this->name_DE;
    }
    
    
    /**
     * @return \AppBundle\Entity\TileShield
     */
    public function getTileShield()
    {
        return $this->tileShield;
    }
    
    /**
     * @var \AppBundle\Entity\TileShield
     */
    public function setTileShield($tileShield)
    {
        $this->tileShield = $tileShield;
    }
    
}
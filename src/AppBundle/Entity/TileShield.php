<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Shield;

/**
 * @OGM\RelationshipEntity(type="HAS_SHIELD")
 */
 
class TileShield
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\AppBundle\Entity\Tile")
     * @var \AppBundle\Entity\Tile
     */
    protected $tile;

    
    /**
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Shield")
     * @var \AppBundle\Entity\Shield
     */
    protected $shield;

    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $hp;    
    

    /**
     * UserResource constructor.
     * @param \AppBundle\Entity\Shield $shield
     * @param \AppBundle\Entity\Building $building
     * @param int $hp
     */
    public function __construct(Shield $shield, Building $building,  $hp)
    {
        $this->tile = $tile;
        $this->shield = $shield;
        $this->hp = $hp;
    }

    public function getId()
    {
        return $this->id;
    }
        
        
    /**
     * @return \AppBundle\Entity\Tile
     */
    public function getTile()
    {
        return $this->tile;
    }
    
    /**
     * @return \AppBundle\Entity\Building
     */
    public function getShield()
    {
        return $this->shield;
    }

        
    /**
     * @var int $hp
     */
    public function setHp($hp)
    {
        $this->hp = $hp;
    }    
    
    /**
     * @return int
     */
    public function getHp()
    {
        return $this->hp;
    }
        
    
}
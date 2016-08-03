<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Building;

/**
 * @OGM\RelationshipEntity(type="HAS_BUILDING")
 */
 
class TileBuilding
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
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Building")
     * @var \AppBundle\Entity\Building
     */
    protected $building;

    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $hp;    
    

    /**
     * UserResource constructor.
     * @param \AppBundle\Entity\Tile $tile
     * @param \AppBundle\Entity\Building $building
     * @param int $hp
     */
    public function __construct(Tile $tile, Building $building,  $hp)
    {
        $this->tile = $tile;
        $this->building = $building;
        $this->hp = $hp;
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
    public function getBuilding()
    {
        return $this->building;
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
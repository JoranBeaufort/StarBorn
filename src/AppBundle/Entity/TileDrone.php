<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Drone;

/**
 * @OGM\RelationshipEntity(type="HAS_DRONE")
 */
 
class TileDrone
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
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Drone")
     * @var \AppBundle\Entity\Drone
     */
    protected $drone;

    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $hp;    
    

    /**
     * UserResource constructor.
     * @param \AppBundle\Entity\Tile $tile
     * @param \AppBundle\Entity\Drone $drone
     * @param int $hp
     */
    public function __construct(Tile $tile, Drone $drone,  $hp)
    {
        $this->tile = $tile;
        $this->drone = $drone;
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
     * @return \AppBundle\Entity\Drone
     */
    public function getDrone()
    {
        return $this->drone;
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
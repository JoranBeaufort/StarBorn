<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Structure;

/**
 * @OGM\RelationshipEntity(type="HAS_STRUCTURE")
 */
 
class TileStructure
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
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Structure")
     * @var \AppBundle\Entity\Structure
     */
    protected $structure;

    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $hp;    
    

    /**
     * UserResource constructor.
     * @param \AppBundle\Entity\Tile $tile
     * @param \AppBundle\Entity\Structure $structure
     * @param int $hp
     */
    public function __construct(Tile $tile, Structure $structure,  $hp)
    {
        $this->tile = $tile;
        $this->structure = $structure;
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
     * @return \AppBundle\Entity\Structure
     */
    public function getStructure()
    {
        return $this->structure;
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
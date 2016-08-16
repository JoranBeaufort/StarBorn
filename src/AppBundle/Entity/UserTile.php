<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\Tile;

/**
 * @OGM\RelationshipEntity(type="CAPTURED")
 */
 
class UserTile
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\JoranBeaufort\Neo4jUserBundle\Entity\User")
     * @var \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    protected $user;

    /**
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Tile")
     * @var \AppBundle\Entity\Tile
     */
    protected $tile;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $captured;    
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $collected;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $resources;     
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $landcover;     


    /**
     * UserResource constructor.
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\User $user
     * @param \AppBundle\Entity\Tile $tile
     * @param int $captured
     * @param int $collected
     */
    public function __construct(User $user, Tile $tile, $captured, $collected, $resources, $landcover)
    {
        $this->user = $user;
        $this->tile = $tile;
        $this->captured = $captured;
        $this->collected = $collected;
        $this->resources = $resources;
        $this->landcover = $landcover;
    }

        
    /**
     * @return \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \AppBundle\Entity\Tile
     */
    public function getTile()
    {
        return $this->tile;
    }
    
    /**
     * @return int
     */
    public function getCaptured()
    {
        return $this->captured;
    }
        
    /**
     * @var int $collected
     */
    public function setCollected($collected)
    {
        $this->collected = $collected;
    }    
    
    /**
     * @return int
     */
    public function getCollected()
    {
        return $this->collected;
    }
       
    public function getResources()
    {
        return $this->resources;
    }
        
    
}
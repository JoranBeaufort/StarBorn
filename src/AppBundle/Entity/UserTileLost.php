<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\Tile;

/**
 * @OGM\RelationshipEntity(type="LOST")
 */
 
class UserTileLost
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
     
    protected $lost;    
    
    /**
     * UserResource constructor.
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\User $user
     * @param \AppBundle\Entity\Tile $tile
     * @param int $collected
     */
    public function __construct(User $user, Tile $tile, $lost)
    {
        $this->user = $user;
        $this->tile = $tile;
        $this->lost = $lost;
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
    public function getLost()
    {
        return $this->lost;
    }
                
    
}
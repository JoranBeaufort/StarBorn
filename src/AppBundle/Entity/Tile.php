<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\UserTile;


/**
 * @OGM\Node(label="Tile")
 */
 
class Tile
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
     
    protected $uid;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $rid;
    
    /**
     * @OGM\Property(type="float")
     * @var float
     */
     
    protected $tLat;
    
    /**
     * @OGM\Property(type="float")
     * @var float
     */
     
    protected $tLng;  
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $resources;    

    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTile", type="CAPTURED", direction="INCOMING", collection=false, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserTile
     */
     
    protected $userTile;    
    
    /**
     * UserResource constructor.
     * @param int $uid
     * @param int $rid
     * @param float $tLat
     * @param float $tLng
     */
     
    public function __construct($uid, $rid, $tLat, $tLng)
    {
        $this->uid = $uid;
        $this->rid = $rid;
        $this->tLat = $tLat;
        $this->tLng = $tLng;
    }

    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUid()
    {
        return $this->uid;
    }
    
    public function getRid()
    {
        return $this->rid;
    }
    
    public function getLat()
    {
        return $this->tLat;
    }
    
    public function getLng()
    {
        return $this->tLng;
    }
    
    public function setResources($resources)
    {
        $this->resources = $resources;
    }
    
    public function getResources()
    {
        return $this->resources;
    }
    
    /**
     * @return \AppBundle\Entity\UserTile
     */
    public function getUserTile()
    {
        return $this->userTile->first();
    }
    
    /**
     * @var \AppBundle\Entity\UserTile
     */
    public function setUserTile($userTile)
    {
        $this->userTile = $userTile;
    }

    
}
<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\UserTile;
use AppBundle\Entity\TileDrone;
use AppBundle\Entity\TileBuilding;
use AppBundle\Entity\TileShield;


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
     
    protected $bBox;  
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $resources;      
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileDrone", type="HAS_DRONE", direction="OUTGOING", collection=false, mappedBy="tile")
     * @OGM\Lazy()
     * @var \AppBundle\Entity\TileDrone
     */
     
    protected $tileDrone;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileBuilding", type="HAS_BUILDING", direction="OUTGOING", collection=false, mappedBy="tile")
     * @OGM\Lazy()
     * @var \AppBundle\Entity\TileBuilding
     */
     
    protected $tileBuilding;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileShield", type="HAS_SHIELD", direction="OUTGOING", collection=false, mappedBy="tile")
     * @OGM\Lazy()
     * @var \AppBundle\Entity\TileShield
     */
     
    protected $tileShield;    

    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTile", type="CAPTURED", direction="INCOMING", collection=true, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserTile[]
     */
     
    protected $userTile;    
    
    /**
     * UserResource constructor.
     * @param int $uid
     * @param int $rid
     * @param float $tLat
     * @param float $tLng
     * @param string $bBox
     */
     
    public function __construct($uid, $rid, $tLat, $tLng, $bBox)
    {
        $this->uid = $uid;
        $this->rid = $rid;
        $this->tLat = $tLat;
        $this->tLng = $tLng;
        $this->bBox = $bBox;
        $this->userTile = new ArrayCollection();

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
    
    public function setBBox($bBox)
    {
        // BB = array([BLX,BLY],[TLX,TLY],[TRX,TRY],[BRX,BRY])
        $this->bBox = $bBox;
    }
    
    public function getBBox($bBox)
    {
        return $this->bBox;
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
        $this->userTile->add($userTile);
    }
    
    
    /**
     * @param \AppBundle\Entity\Drone $drone
     * @param int $hp
     */
    public function setTileDrone(Drone $drone, $hp)
    {
        $td = new TileDrone($this, $drone, $hp);
        $this->tileDrone = $td;
        $drone->setTileDrone($td);
    }
    
    /**
     * @return \AppBundle\Entity\TileDrone
     */
    public function getTileDrone()
    {
        return $this->tileDrone;
    }
    
    /**
     * @param \AppBundle\Entity\Building $building
     * @param int $hp
     */
    public function setTileBuilding(Building $building, $hp)
    {
        $tb = new TileBuilding($this, $building, $hp);
        $this->tileBuilding = $tb;
        $drone->setTileBuilding($tb);
    }
    
    /**
     * @return \AppBundle\Entity\TileBuilding
     */

    public function getTileBuilding()
    {
        return $this->tileBuilding;
    }
    
    /**
     * @param \AppBundle\Entity\Shield $shield
     * @param int $hp
     */
    public function setTileShield(Shield $shield, $hp)
    {
        $ts = new TileShield($this, $shield, $hp);
        $this->tileShield = $ts;
        $drone->setTileShield($ts);
    }
    
    /**
     * @return \AppBundle\Entity\TileShield
     */

    public function getTileShield()
    {
        return $this->tileShield;
    }
}
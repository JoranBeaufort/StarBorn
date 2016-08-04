<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\UserTile;
use AppBundle\Entity\UserTileLost;
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
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileDrone", type="HAS_DRONE", direction="OUTGOING", collection=true, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileDrone[]
     */
     
    protected $tileDrone;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileBuilding", type="HAS_BUILDING", direction="OUTGOING", collection=true, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileBuilding[]
     */
     
    protected $tileBuilding;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileShield", type="HAS_SHIELD", direction="OUTGOING", collection=true, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileShield[]
     */
     
    protected $tileShield;    

    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTile", type="CAPTURED", direction="INCOMING", collection=true, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserTile[]
     */
     
    protected $userTile;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTileLost", type="LOST", direction="INCOMING", collection=true, mappedBy="tile")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\UserTileLost[]
     */
     
    protected $userTileLost;    
    
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
        $this->userTileLost = new ArrayCollection();
        $this->tileShield = new ArrayCollection();
        $this->tileBuilding = new ArrayCollection();
        $this->tileDrone = new ArrayCollection();
        
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
     * @param \AppBundle\Entity\UserTile $userTile
     */
    public function removeUserTile(UserTile $userTile)
    {
        if ($this->userTile->contains($userTile)) {
            $this->userTile->removeElement($userTile);
        }
    }
    
    /**
     * @return \AppBundle\Entity\UserTileLost
     */
    public function getUserTileLost()
    {
        return $this->userTileLost->first();
    }
    
    /**
     * @var \AppBundle\Entity\UserTileLost
     */
    public function setUserTileLost($userTileLost)
    {
        $this->userTileLost->add($userTileLost);
    }
    
    /**
     * @param \AppBundle\Entity\UserTileLost $userTileLost
     */
    public function removeUserTileLost(UserTileLost $userTileLost)
    {
        $this->userTileLost->removeElement($userTileLost);
    }

    
    /**
     * @param \AppBundle\Entity\Drone $drone
     * @param int $hp
     */
    public function setTileDrone(Drone $drone, $hp)
    {
        $td = new TileDrone($this, $drone, $hp);
        $this->tileDrone->add($td);
        $drone->setTileDrone($td);
    }
    
    /**
     * @return \AppBundle\Entity\TileDrone
     */
    public function getTileDrone()
    {
        return $this->tileDrone->first();
    }
    
    /**
     * @param \AppBundle\Entity\TileDrone $tileDrone
     */
    public function removeTileDrone(TileDrone $tileDrone)
    {
        $this->tileDrone->removeElement($tileDrone);
        $tileDrone->getDrone()->removeTileDrone($tileDrone);
    }
    
    /**
     * @param \AppBundle\Entity\Building $building
     * @param int $hp
     */
    public function setTileBuilding(Building $building, $hp)
    {
        $tb = new TileBuilding($this, $building, $hp);
        $this->tileBuilding->add($tb);
        $building->setTileBuilding($tb);
    }
    
    /**
     * @param \AppBundle\Entity\TileBuilding $tileBuilding
     */
    public function removeTileBuilding(TileBuilding $tileBuilding)
    {
        $this->tileBuilding->removeElement($tileBuilding);
        $tileBuilding->getBuilding()->removeTileBuilding($tileBuilding);
    }
    
    /**
     * @return \AppBundle\Entity\TileBuilding
     */

    public function getTileBuilding()
    {
        return $this->tileBuilding->first();
    }
    
    /**
     * @param \AppBundle\Entity\Shield $shield
     * @param int $hp
     */
    public function setTileShield(Shield $shield, $hp)
    {
        $ts = new TileShield($this, $shield, $hp);
        $this->tileShield->add($ts);
        $shield->setTileShield($ts);
    }
    
    /**
     * @param \AppBundle\Entity\TileShield $tileShield
     */
    public function removeTileShield(TileShield $tileShield)
    {
        $this->tileShield->removeElement($tileShield);
        $tileShield->getShield()->removeTileShield($tileShield);
    }
    
    /**
     * @return \AppBundle\Entity\TileShield
     */

    public function getTileShield()
    {
        return $this->tileShield->first();
    }
}
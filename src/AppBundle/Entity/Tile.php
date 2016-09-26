<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;


 
/**
 * @OGM\Node(label="Tile")
 */
 
class Tile
{
    /**
     * @OGM\GraphId()
     */
    protected $id;
        
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $tid;    
    
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
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileStructure", type="HAS_STRUCTURE", direction="OUTGOING", collection=true, mappedBy="tile")
     * @var ArrayCollection|\AppBundle\Entity\TileStructure[]
     */
     
    protected $tileStructures;    
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTile", type="CAPTURED", direction="INCOMING", collection=true, mappedBy="tile")
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
     * @param int $rid
     * @param float $tLat
     * @param float $tLng
     * @param string $bBox
     */
     
    public function __construct($tid, $rid, $tLat, $tLng, $bBox)
    {
        $this->tid = $tid;
        $this->rid = $rid;
        $this->tLat = $tLat;
        $this->tLng = $tLng;
        $this->bBox = $bBox;
        $this->userTile = new ArrayCollection();
        $this->userTileLost = new ArrayCollection();
        $this->tileStructures = new ArrayCollection();
        
    }

    
    public function getId()
    {
        return $this->id;
    }
    
    public function getTid()
    {
        return $this->tid;
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
        return $this;
    }
    
    /**
     * @param \AppBundle\Entity\UserTile $userTile
     */
    public function removeUserTile(UserTile $userTile)
    {
        $this->userTile->removeElement($userTile);
        return $this;
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
    public function setUserTileLost(UserTileLost $userTileLost)
    {
        $this->userTileLost->add($userTileLost);
        return $this;
    }
    
    /**
     * @param \AppBundle\Entity\UserTileLost $userTileLost
     */
    public function removeUserTileLost(UserTileLost $userTileLost)
    {
        $this->userTileLost->removeElement($userTileLost);
        return $this;
    }

    
    /**
     * @param \AppBundle\Entity\Structure $structure
     * @param int $hp
     */
    public function addTileStructure(Structure $structure)
    {
        $hp = $structure->getHp();
        $td = new TileStructure($this, $structure, $hp);
        $this->tileStructures->add($td);
        $structure->addTileStructure($td);
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\TileStructure[]
     */
    public function getTileStructures()
    {
        $iterator = $this->tileStructures->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getStructure()->getBlueprintStructure()->getBlueprint()->getIlvl() < $b->getStructure()->getBlueprintStructure()->getBlueprint()->getIlvl()) ? 1 : -1;
        });
        $collection = new ArrayCollection(iterator_to_array($iterator));

        return $collection;
    }
    
    /**
     * @return \AppBundle\Entity\TileStructure
     */
    public function getTileStructureByType($type)
    {
        foreach($this->tileStructures as $structure){
            if($structure->getStructureType() == $type){
                return $structure;
            }
        }
    }
    
    /**
     * @param \AppBundle\Entity\TileStructure $tileStructure
     */
    public function removeTileStructure(TileStructure $tileStructure)
    {
        $this->tileStructures->removeElement($tileStructure);
        $tileStructure->getStructure()->removeTileStructure($tileStructure);
        return $this;
    }
    
}
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
     * @var int
     */
    protected $id;
        
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
     * @var AppBundle\Entity\UserTile[]
     *
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTile", direction="INCOMING", collection=true, mappedBy="tile")
     */
     
    protected $memberships;    
    
    /**
     * UserResource constructor.
     * @param float $rid
     * @param float $tLat
     * @param float $tLng
     */
     
    public function __construct($rid, $tLat, $tLng)
    {
        $this->rid = $rid;
        $this->tLat = $tLat;
        $this->tLng = $tLng;
        $this->memberships = new ArrayCollection();
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
     * @return \AppBundle\Entity\UserTile[]
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    
}
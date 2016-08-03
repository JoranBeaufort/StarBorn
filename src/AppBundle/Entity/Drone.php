<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;
 
use AppBundle\Entity\Tile;
use AppBundle\Entity\TileDrone;


/**
 * @OGM\Node(label="Drone")
 */
 
class Drone
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
     
    protected $did;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $hp;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $img;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name_DE;
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\TileDrone", type="HAS_DRONE", direction="INCOMING", collection=true, mappedBy="drone")
     * @OGM\Lazy()
     * @var ArrayCollection|\AppBundle\Entity\TileDrone
     */
     
    protected $tileDrone;  
    
    public function __construct()
    {
        $this->tileDrone = new ArrayCollection();
    }

    
    public function getId()
    {
        return $this->id;
    }
    
    public function getDid()
    {
        return $this->did;
    }
    
    public function getHp()
    {
        return $this->hp;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getImg()
    {
        return $this->img;
    }
    
    public function getName_DE()
    {
        return $this->name_DE;
    }
    
    
    /**
     * @return \AppBundle\Entity\TileDrone
     */
    public function getTileDrone()
    {
        return $this->tileDrone;
    }
    
    /**
     * @var \AppBundle\Entity\TileDrone
     */
    public function setTileDrone($tileDrone)
    {
        $this->tileDrone->add($tileDrone);
    }
    
    /**
     * @var \AppBundle\Entity\TileDrone
     */
    public function removeTileDrone($tileDrone)
    {
        $this->tileDrone->removeElement($tileDrone);
    }
    
}
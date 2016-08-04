<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Resources")
 */
 
class Resources
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
        
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $resourceType;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name_DE;    
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $icon;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $iconColour;
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $colour; 

     /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserResource",type="HAS_RESOURCE", direction="INCOMING", collection=true, mappedBy="resources")
     * @var ArrayCollection|\AppBundle\Entity\UserResource[]
     */
    protected $userResources;    
    
    public function __construct()
    {
        $this->userResources = new ArrayCollection();
    }

    
    public function getId()
    {
        return $this->id;
    }
    
    public function getResourceType()
    {
        return $this->resourceType;
    }

    
    public function getName_DE()
    {
        return $this->name_DE;
    }

    
    public function getIcon()
    {
        return $this->icon;
    }

    
    public function getIconColour()
    {
        return $this->iconColour;
    }
    
    public function getColour()
    {
        return $this->colour;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\AppBundle\Entity\UserResource[]
     */
    public function getUserResources()
    {
        return $this->userResources;
    }
    
}
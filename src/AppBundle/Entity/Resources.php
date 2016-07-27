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
     * @var AppBundle\Entity\UserResource[]
     *
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserResource",type="HAS_RESOURCE", direction="INCOMING", collection=true, mappedBy="resources")
     */
    protected $memberships;    
    
    public function __construct()
    {
        $this->memberships = new ArrayCollection();
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
     * @return \AppBundle\Entity\UserResource[]
     */
    public function getMemberships()
    {
        return $this->memberships;
    }
    
}
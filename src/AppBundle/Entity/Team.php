<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Team")
 */
 
class Team
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $tid;
        
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name;  
    
    /**
     * @var AppBundle\Entity\UserTeam[]
     *
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTeam", direction="INCOMING", collection=true, mappedBy="team")
     */
    protected $memberships;
    
    public function __construct($teamname)
    {
        $this->name = $teamname;
        $this->memberships = new ArrayCollection();
    }
    
    
    public function getName()
    {
        return $this->name;
    }  
    
    public function getId()
    {
        return $this->id;
    }    
    
    public function getTid()
    {
        return $this->tid;
    }    
    
    
    /**
     * @return \AppBundle\Entity\UserTeam[]
     */
    public function getMemberships()
    {
        return $this->memberships;
    }
}
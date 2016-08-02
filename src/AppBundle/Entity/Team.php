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
     * @var AppBundle\Entity\UserTeam
     *
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTeam", direction="INCOMING", collection=false, mappedBy="team")
     */
    protected $userTeam;
    
    public function __construct($tid,$teamname)
    {
        $this->tid = $tid;
        $this->name = $teamname;
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
     * @var \AppBundle\Entity\UserTeam
     */
    public function addUserTeam($userTeam)
    {
        return $this->userTeam = $userTeam;
    }
    
    /**
     * @return \AppBundle\Entity\UserTeam
     */
    public function getUserTeam()
    {
        return $this->userTeam;
    }
}
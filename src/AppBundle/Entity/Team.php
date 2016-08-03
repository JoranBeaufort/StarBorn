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
     * @OGM\Property(type="int")
     * @var int
     */
    protected $tid;
        
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $name;  
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTeam", direction="INCOMING", collection=true, mappedBy="team")
     * @var ArrayCollection|\AppBundle\Entity\UserTeam[]
     */
    protected $userTeam;
    
    public function __construct($tid,$teamname)
    {
        $this->tid = $tid;
        $this->name = $teamname;
        $this->userTeam = new ArrayCollection();
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
        return $this->userTeam->add($userTeam);
    }
    
    /**
     * @return \AppBundle\Entity\UserTeam
     */
    public function getUserTeam()
    {
        return $this->userTeam;
    }
}
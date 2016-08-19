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
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $img;  
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $title;  
    
    /**
     * @OGM\Property(type="string")
     * @var string
     */
     
    protected $txt;  
    
    /**
     * @OGM\Relationship(relationshipEntity="\AppBundle\Entity\UserTeam", type="IN_TEAM", direction="INCOMING", collection=true, mappedBy="team")
     * @var ArrayCollection|\AppBundle\Entity\UserTeam[]
     */
    protected $userTeam;
    
    public function __construct()
    {
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
    
    public function getImg()
    {
        return $this->img;
    }     
    
    
    public function getTitle()
    {
        return $this->title;
    }    
    
    
    public function getTxt()
    {
        return $this->txt;
    }    
    
    
    /**
     * @param \AppBundle\Entity\UserTeam $userTeam
     */
    public function addUserTeam(UserTeam $userTeam)
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
<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\Team;

/**
 * @OGM\RelationshipEntity(type="IN_TEAM")
 */
 
class UserTeam
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\JoranBeaufort\Neo4jUserBundle\Entity\User")
     * @var \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    protected $user;

    /**
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Team")
     * @var \AppBundle\Entity\Team
     */
    protected $team;
    
    /**
     * @OGM\Property(type="int")
     * @var int
     */
     
    protected $joined;    


    /**
     * UserResource constructor.
     * @param \JoranBeaufort\Neo4jUserBundle\Entity\User $user
     * @param \AppBundle\Entity\Team $team
     * @param int $joined
     */
    public function __construct(User $user, Team $team, $joined)
    {
        $this->user = $user;
        $this->team = $team;
        $this->joined = $joined;
    }

        
    /**
     * @return \JoranBeaufort\Neo4jUserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \AppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    
    /**
     * @return int
     */
    public function getJoined()
    {
        return $this->joined;
    }
        
    
}
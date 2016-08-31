<?php
namespace AppBundle\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\RelationshipEntity(type="BUILDS")
 */
 
class BlueprintStructure
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;
    
    /**
     * @OGM\StartNode(targetEntity="\AppBundle\Entity\Blueprint")
     * @var \AppBundle\Entity\Blueprint
     */
     
    protected $blueprint;

    /**
     * @OGM\EndNode(targetEntity="\AppBundle\Entity\Structure")
     * @var \AppBundle\Entity\Structure
     */
    protected $structure;    


    public function __construct()
    {
    }

        
    /**
     * @return \AppBundle\Entity\Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @return \AppBundle\Entity\Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    
}
<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Tile;

class OverviewController extends Controller
{
    public function indexAction()
    {    
        $user=$this->getUser();
        $em = $this->get('neo4j.graph_manager')->getClient();

        $result = $em->getDatabaseDriver()->run("match (m:Team)<-[:IN_TEAM]-(u:User)-[:CAPTURED]->(t:Tile) return t.bBox, m.name");

        $tiles = array();
        foreach($result->records() as $record){
            array_push($tiles,$record->values());
        }


        return $this->render(
            'AppBundle:Overview:overview.html.twig',array('user'=>$user, 'tiles' => $tiles)
        );
    }
    
}
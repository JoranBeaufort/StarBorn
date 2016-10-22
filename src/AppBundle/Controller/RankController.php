<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Tile;
use JoranBeaufort\Neo4jUserBundle\Entity\User;


class RankController extends Controller
{
    public function indexAction()
    {

        $em = $this->get('neo4j.graph_manager')->getClient();

        $result = $em->getDatabaseDriver()->run("match (u:User), (u)-[:CAPTURED]->(t) optional match (u)-[:LOST]->(t1) with u, count(t) as cntC, count(t1) as cntL return u.screenname, cntC, cntL");

        $ranklist = array();
        foreach($result->records() as $record){
            array_push($ranklist,$record->values());
        }

        // Mprint_r($ranklist);die;

        foreach($ranklist as $rank){
            $rank[2] += $rank[1];
            }

        // print_r($ranklist);die;

        usort($ranklist, function($a, $b) {
            return $a[2] <= $b[2];
        });


        return $this->render(
            'AppBundle:Rank:rank.html.twig',array('ranklist' => $ranklist)
        );
    }
}
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

        $result = $em->getDatabaseDriver()->run("match (u:User)-[r:CAPTURED|LOST]->(t) with u,t return u.screenname,count(t)");

        $ranklist = array();
        foreach($result->records() as $record){
            array_push($ranklist,$record->values());
        }

        // Mprint_r($ranklist);die;

        /* foreach($ranklist as $rank){
            $rank[2] += $rank[1];
            }
        */
        // print_r($ranklist);die;

        usort($ranklist, function($a, $b) {
            return $a[1] <= $b[1];
        });


        $result = $em->getDatabaseDriver()->run("match (t:Team)<-[:IN_TEAM]-(u:User)-[:CAPTURED]->(r:Tile) with t,r return t.name, count(r)");

        $teamlist = array();
        foreach($result->records() as $record){
            array_push($teamlist,$record->values());
        }

        // print_r($teamlist);die;

        return $this->render(
            'AppBundle:Rank:rank.html.twig',array('ranklist' => $ranklist, 'teamlist' => $teamlist)
        );
    }
}
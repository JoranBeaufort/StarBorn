<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ActivityController extends Controller
{
    public function indexAction(Request $request)
    {

/*
         if($this->getUser()->getUsername() != 'mfbaer'){
            echo "work in progress on this page"; die;
        }
*/
        /**
         *  @var $userTile \AppBundle\Entity\UserTile
         *  @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
         */
        $em = $this->get('neo4j.graph_manager')->getClient();
        // $nr = $em->getDatabaseDriver()->run("match (u1:User)-[c:CAPTURED]->(t1) with collect({t:'c', lat:t1.tLat, lng:t1.tLng, name:u1.screenname, time:TOINT(c.captured)}) as rows  match (u2:User)-[l:LOST]->(t2) with rows +  collect({t:'l', lat:t2.tLat, lng:t2.tLng, name:u2.screenname, time:TOINT(l.lost)}) as allRows UNWIND allRows as row with row.t as t, row.lat as lat, row.lng as lng, row.name as name, row.time as time  return t,lat,lng,name,time ORDER BY time DESC limit 30");

        $captured = $em->getDatabaseDriver()->run("match (u1:User)-[c:CAPTURED]->(t1) return 'c', t1.tLat,t1.tLng,u1.username,c.captured ORDER BY toint(c.captured)  DESC limit 20");


        $activityArray = array();

        $user = $this->getUser();

        foreach($captured->records() as $record){
            array_push($activityArray,$record->values());
        }


        $lost = $em->getDatabaseDriver()->run("match (u1:User)-[l:LOST]->(t1) where toint(l.lost)>=".intval($activityArray[19][4])." return 'l', t1.tLat,t1.tLng,u1.username,l.lost");

        foreach($lost->records() as $record){
            array_push($activityArray,$record->values());
        }

        usort($activityArray, function($a, $b) {
            return $a[4] <= $b[4];
        });


        // var_dump($activityArray);die;

        return $this->render('AppBundle:Activity:activity.html.twig',array('user' => $user, 'activities' => $activityArray));

    }
}
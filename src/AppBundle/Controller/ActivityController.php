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
        $nr = $em->getDatabaseDriver()->run("match (u1:User)-[c:CAPTURED]->(t1) with collect({t:'c', lat:t1.tLat, lng:t1.tLng, name:u1.screenname, time:TOINT(c.captured)}) as rows  match (u2:User)-[l:LOST]->(t2) with rows +  collect({t:'l', lat:t2.tLat, lng:t2.tLng, name:u2.screenname, time:TOINT(l.lost)}) as allRows UNWIND allRows as row with row.t as t, row.lat as lat, row.lng as lng, row.name as name, row.time as time  return t,lat,lng,name,time ORDER BY time DESC limit 30");

        $activityArray = array();


        $user = $this->getUser();

        foreach($nr->records() as $record){
            array_push($activityArray,$record->values());
        }

        // var_dump($activityArray);die;

        return $this->render('AppBundle:Activity:activity.html.twig',array('user' => $user, 'activities' => $activityArray));

    }
}
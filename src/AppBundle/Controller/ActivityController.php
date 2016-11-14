<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ActivityController extends Controller
{
    public function indexAction(Request $request)
    {
        /**
         *  @var $userTile \AppBundle\Entity\UserTile
         *  @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
         */


        $activityArray = array();

        $user = $this->getUser();

        foreach($user->getUserTiles() as $userTile) {
            $tileArray = array();
            array_push($tileArray, $userTile->getCaptured());
            array_push($tileArray, 'captured');
            array_push($tileArray, $userTile->getTile());
            array_push( $activityArray,$tileArray);
        }

        foreach($user->getUserTilesLost() as $userTileLost){
            $tileLostArray = array();
            array_push($tileLostArray,$userTileLost->getLost());
            array_push($tileLostArray, 'lost');
            array_push($tileLostArray, $userTileLost->getTile());
            array_push( $activityArray,$tileLostArray);
        }

        usort($activityArray, function($a, $b) {
            return $a[0] <= $b[0];
        });

        return $this->render('AppBundle:Activity:activity.html.twig',array('user' => $user, 'activities' => $activityArray));

    }
}
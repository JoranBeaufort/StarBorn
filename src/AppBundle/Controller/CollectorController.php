<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Tile;

use JoranBeaufort\Neo4jUserBundle\Entity\User;

class CollectorController extends Controller
{
    public function indexAction(Request $request)
    {

        $em = $this->get('neo4j.graph_manager')->getClient();
        $em->clear();

        $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());

        return $this->render('AppBundle:Collector:collect.html.twig',array('user' => $user));

    }
}
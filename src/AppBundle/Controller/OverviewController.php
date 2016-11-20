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

        $tiles = $em->getRepository(Tile::class)->findAll();
        
        return $this->render(
            'AppBundle:Overview:overview.html.twig',array('user'=>$user, 'tiles' => $tiles)
        );
    }
    
}
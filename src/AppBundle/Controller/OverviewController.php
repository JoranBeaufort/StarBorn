<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Tile;

class OverviewController extends Controller
{
    public function indexAction()
    {    
        
        $em = $this->get('neo4j.graph_manager')->getClient();

        $tiles = $em->getRepository(Tile::class)->findAll();
        
        return $this->render(
            'AppBundle:Overview:overview.html.twig',array('tiles' => $tiles)
        );
    }
    
}
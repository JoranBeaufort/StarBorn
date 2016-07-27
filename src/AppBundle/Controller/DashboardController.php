<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Resources;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class DashboardController extends Controller
{
    public function indexAction()
    {
        
        $em = $this->get('neo4j.graph_manager')->getClient();        
        $user=$em->getRepository(User::class)->findOneBy('usernameCanonical', $this->getUser()->getUsernameCanonical());
        
        /*
        $resources=$user->getUserResource('stone');
        $resources->setAmount(54);
        $em->persist($user);
        $em->flush();            
        
        var_dump($user->getUserResource('stone')->getAmount());
        die;
        foreach($resources as $r){
            var_dump($r->getResource()->getResourceType());
        }
        die;
        */
        return $this->render(
            'AppBundle:Dashboard:index.html.twig',array('user' => $user)
        );
    }
}
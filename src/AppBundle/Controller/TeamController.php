<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;

use AppBundle\Form\TeamApplyType;
use AppBundle\Entity\Team;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class TeamController extends Controller
{
    public function applyAction(Request $request)
    {    
        $em = $this->get('neo4j.graph_manager')->getClient();
        $user=$this->getUser();
        
        if($user->getUserTeam() === null || $user->getUserTeam() === false){           
            
            $form = $this->createForm(TeamApplyType::class);        
            $form->handleRequest($request);
            // var_dump($user->getUserTeam());die;
            
            if ($form->isSubmitted() && $form->isValid()) {
                $team_selected = $form->get('teamapply')->getData();      
                $team = $em->getRepository(Team::class)->findOneBy('name', $team_selected);  
                $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());
                $user->addTeam($team, time());
                $em->flush();  
                
                $url = $this->generateUrl('dashboard');            
                return new RedirectResponse($url);
            }

            
            return $this->render('AppBundle:Team:apply.html.twig',array('user' => $user,'form' => $form->createView()));
        }else{
            return $this->render('AppBundle:Team:exists.html.twig',array('user' => $user));
        }
   }
}
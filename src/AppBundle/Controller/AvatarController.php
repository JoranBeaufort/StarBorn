<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Form\TeamApplyType;
use AppBundle\Entity\Team;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class AvatarController extends Controller
{
    public function creatorAction()
    {    
        $user=$this->getUser();
        return $this->render('AppBundle:Avatar:creator.html.twig',array('user' => $user));
   }
   
    public function uploadAction(Request $request)
    {    
        $user = $this->getUser();
        $em = $this->get('neo4j.graph_manager')->getClient();
        $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());
        
        $imgdata = $request->request->get('imgdata');
        $filename = $request->request->get('filename');
        $userid = $request->request->get('userid');
        
        if($user->getUid() == $userid && !$user->getProfileImage()){

            
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgdata));
            file_put_contents($this->getParameter('neo4j_user.directory').'/'.$user->getUid().'/'.$filename, $data);
            
            $user->setProfileImage($filename);
            
            $em->flush();

            $response = new Response('saved');
            $response->headers->set('Content-Type', 'text/html');

            return $response;
        }else{
            $response = new Response('fail');
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }
   }
}
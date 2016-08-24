<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;

use AppBundle\Entity\Resources;
use AppBundle\Entity\Blueprint;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class StoreController extends Controller
{
    public function indexAction(Request $request)
    {    
        $em = $this->get('neo4j.graph_manager')->getClient();
        $user = $this->getUser();  
        
        $blueprints = $em->getRepository(Blueprint::class)->findAll();
        
        
        return $this->render('AppBundle:Store:store.html.twig',array('user' => $user, 'blueprints'=>$blueprints));
        
    }
}
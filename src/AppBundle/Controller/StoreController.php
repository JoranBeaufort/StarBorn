<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Blueprint;

class StoreController extends Controller
{
    public function indexAction(Request $request)
    {    
        $em = $this->get('neo4j.graph_manager')->getClient();
        $user = $this->getUser();  
        
        $blueprints = new ArrayCollection($em->getRepository(Blueprint::class)->findAll());

        $iterator = $blueprints->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getIlvl() < $b->getIlvl()) ? -1 : 1;
        });

        $collection = new ArrayCollection(iterator_to_array($iterator));
        
        
        return $this->render('AppBundle:Store:store.html.twig',array('user' => $user, 'blueprints'=>$collection));
        
    }
}
<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;

use AppBundle\Form\CaptureInterfaceType;
use AppBundle\Entity\Resources;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class InventoryController extends Controller
{
    public function indexAction(Request $request)
    {    
        $user = $this->getUser();               
        
        return $this->render('AppBundle:Inventory:inventory.html.twig',array('user' => $user));
        
    }
}
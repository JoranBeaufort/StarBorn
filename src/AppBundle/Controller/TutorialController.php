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

class TutorialController extends Controller
{
    public function indexAction(Request $request)
    {
            return $this->render('AppBundle:Tutorial:tutorial.html.twig');
    }
}
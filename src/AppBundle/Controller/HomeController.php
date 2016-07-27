<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();
        return $this->render(
            'AppBundle:Home:index.html.twig',array('user' => $user)
        );
    }
}
<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StoryController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'AppBundle:Story:index.html.twig'
        );
    }
}
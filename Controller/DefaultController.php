<?php

namespace gsiwerd\MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MyBundle:Default:index.html.twig');
    }
}

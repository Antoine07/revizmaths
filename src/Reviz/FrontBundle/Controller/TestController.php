<?php

namespace Reviz\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller {

    /**
     * @Route("/", name="index")
     */
    public function indexAction() {
        
        return $this->render('RevizFrontBundle:Front:index.html.twig');
    }
}
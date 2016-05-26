<?php

namespace Reviz\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FrontController extends Controller
{
      /**
       * @Route("/", name="home")
       */
      public function indexAction() {

            return $this->render('RevizFrontBundle:Front:accueil.html.twig');
      }
}
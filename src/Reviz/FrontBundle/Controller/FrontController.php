<?php

namespace Reviz\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FrontController extends Controller
{
    public function menuAction() {
        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Partials:nav.html.twig', ['levels' => $levels]);
    }



    /**
     * @Route("/", name="home")
     */
    public function indexAction() {
        
        return $this->render('RevizFrontBundle:Front:home.html.twig');
    }


    /**
     * @Route("/level/{id}", name="level")
     */
    public function showLevelAction() {

        return $this->render('RevizFrontBundle:Front:showlevel.html.twig');
    }


    /**
     * @Route("/questions", name="questions")
     */
    public function questionsAction() {

        return $this->render('RevizFrontBundle:Front:faq.html.twig');
    }


    /**
     * @Route("/about", name="about")
     */
    public function aboutAction() {

        return $this->render('RevizFrontBundle:Front:about.html.twig');
    }


    /**
     * @Route("/contacts", name="contacts")
     */
    public function contactsAction() {

        return $this->render('RevizFrontBundle:Front:contacts.html.twig');
    }
}
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
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Front:home.html.twig', ['levels' => $levels]);
    }


    /**
     * @Route("/level/{id}", name="level")
     */
    public function showLevelAction() {
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Front:showlevel.html.twig', ['levels' => $levels]);
    }


    /**
     * @Route("/questions", name="questions")
     */
    public function questionsAction() {
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Front:faq.html.twig', ['levels' => $levels]);
    }


    /**
     * @Route("/about", name="about")
     */
    public function aboutAction() {
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Front:about.html.twig', ['levels' => $levels]);
    }


    /**
     * @Route("/contacts", name="contacts")
     */
    public function contactsAction() {
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Front:contacts.html.twig', ['levels' => $levels]);
    }
}
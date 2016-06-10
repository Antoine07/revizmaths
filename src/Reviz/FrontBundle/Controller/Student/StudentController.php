<?php

namespace Reviz\FrontBundle\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends Controller
{
    /**
     * @Route("/student/dashboard", name="student_homepage")
     */
    public function indexAction()
    {
        $modules = [];

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $student = $em->getRepository('RevizFrontBundle:User')->find($user->getId());
        $commands = $student->getCommands();
        foreach ($commands as $command) {
            $categories = $em->getRepository('RevizFrontBundle:Taxonomy')
                ->allCategoriesByModule($command->getTaxonomy()->getId()); // find() est un objet

            $modules [$command->getTaxonomy()->getName()] = $categories;
        }
        // dump($modules); die;

        /*$posts = $module->getPosts();
        foreach ($posts as $post) {
            $data[] = $post->getTitle();
        }*/

        return $this->render('RevizFrontBundle:Student:student-home.html.twig', [
            'student' => $student,
            "modules" => $modules,
        ]);
    }

    /**
     * @Route("/student/dashboard/settings", name="student_settings")
     */
    public function settingsAction(Request $request) {

        $formSettings = $this->createFormBuilder()
            ->add('nom', TextType::class)
            ->add('email', EmailType::class)
            ->add('mot_de_passe', PasswordType::class)
            ->add('confimer_password', PasswordType::class)
            ->getForm()
        ;

        $formSettings->handleRequest($request);

        if ($formSettings->isSubmitted() && $formSettings->isValid()) {

            return $this->redirectToRoute('student_settings');
        }

        return $this->render('RevizFrontBundle:Student:student-settings.html.twig', array(
            'form_settings' => $formSettings->createView()
        ));
    }

}
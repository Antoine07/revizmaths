<?php

namespace Reviz\FrontBundle\Controller\Student;

use Reviz\FrontBundle\Form\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        // TODO Gérer les checkboxes pour filtrer les contenus de l'utilisateurs

        $formSettings = $this->createForm(SettingsType::class);

        $formSettings->handleRequest($request);

        if ($formSettings->isSubmitted() && $formSettings->isValid()) {

            $result = $formSettings->getData();

            $username = $result['username'];
            $email = $result['email'];
            $password = $result['password'];

            $userManager = $this->container->get('fos_user.user_manager');
            $currentUser = $this->getUser()->getId();
            $user = $userManager->findUserBy(['id' => $currentUser]);

            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPlainPassword($password);

            $userManager->updateUser($user);

            return $this->redirectToRoute('student_settings');
            
        }

        return $this->render('RevizFrontBundle:Student:student-settings.html.twig', array(
            'form_settings' => $formSettings->createView()
        ));
    }

}
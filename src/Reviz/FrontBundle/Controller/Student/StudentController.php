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

        return $this->render('RevizFrontBundle:Student:student-home.html.twig', [
            'student' => $student,
            "modules" => $modules,
        ]);
    }

    /**
     * @Route("/student/dashboard/category/{id}", name="student_category_show", requirements={"id" = "\d+"})
     */
    public function showCategoryAction($id) {
       
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('RevizFrontBundle:Category');
        $category = $repo->find(array('id' => $id));
        $cat = $category->getParentId();

        $rep = $em->getRepository('RevizFrontBundle:module');
        $module = $rep->find(array('id' => $cat));

        $repository = $em->getRepository('RevizFrontBundle:Post');
        $courses = $repository->allPostByTermId($category, 'Course');
        $exercices = $repository->allPostByTermId($category, 'Exercice');
        $methods =$repository->allPostByTermId($category, 'Method');
        
        return $this->render('RevizFrontBundle:Student:show-category.html.twig', array(
            'module' => $module,
            'category' => $category,
            'courses' => $courses,
            'exercices' => $exercices,
            'methods' => $methods,
        ));

    }

    /**
     * @Route("/student/dashboard/method/{id}", name="student_method_show")
     */
    public function showMethodAction($id) {

        $repo = $this->getDoctrine()->getRepository('RevizFrontBundle:Post');
        $method = $repo->find(['id' => $id]);

        $category = $this->getDoctrine()
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->getCategory($method->getId());


        return $this->render('RevizFrontBundle:Student:show-method.html.twig', array(
            'method' => $method,
            'category' => $category,
        ));

    }

    /**
     * @Route("/student/dashboard/course/{id}", name="student_course_show")
     */
    public function showCourseAction($id) {

        $repo = $this->getDoctrine()->getRepository('RevizFrontBundle:Post');
        $course = $repo->find(['id' => $id]);

        $category = $this->getDoctrine()
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->getCategory($course->getId());

        return $this->render('RevizFrontBundle:Student:show-course.html.twig', array(
            'course' => $course,
            'category' => $category,
        ));

    }

    /**
     * @Route("/student/dashboard/exercice/{id}", name="student_exercice_show")
     */
    public function showExerciceAction($id) {

        $repo = $this->getDoctrine()->getRepository('RevizFrontBundle:Post');
        $exercice = $repo->find(['id' => $id]);

        $category = $this->getDoctrine()
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->getCategory($exercice->getId());
        
        return $this->render('RevizFrontBundle:Student:show-exercice.html.twig', array(
            'exercice' => $exercice,
            'category' => $category,
        ));
        
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
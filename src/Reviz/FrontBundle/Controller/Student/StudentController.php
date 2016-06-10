<?php

namespace Reviz\FrontBundle\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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

        return $this->render('RevizFrontBundle:Front:student.html.twig', [
            'student' => $student,
            "modules" => $modules,
        ]);
    }

}
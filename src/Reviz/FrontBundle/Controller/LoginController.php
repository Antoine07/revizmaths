<?php

namespace Reviz\FrontBundle\Controller;

use Doctrine\ORM\Mapping\Id;
use Reviz\FrontBundle\Repository\UserRepository;
use Reviz\FrontBundle\RevizFrontBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller {

    /**
     * @Route("/users", name="index")
     */
    public function indexAction() {

        $repository = $this->getDoctrine()
            ->getRepository('RevizFrontBundle:User');

        $users = $repository->findAll();

        return $this->render('RevizFrontBundle:Front:index.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/users/activate/{id}", name="activate")
     */
    public function activateUserAction($id) {

        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserBy(['id' => $id]);

        if ($user->isEnabled() !== true) {
            $user->setEnabled(true);
        }
        elseif ($user->isEnabled() == true) {
            $user->setEnabled(false);
        }

        $userManager->updateUser($user);

        return $this->redirect('/users');
    }

    /**
     * @Route("/admin/dashboard", name="back_homepage")
     */
    public function getBackOfficeAction() {
        
        return $this->render('RevizFrontBundle:Front:admin.html.twig');
    }

    /**
     * @Route("/student/dashboard", name="student_homepage")
     */
    public function getStudentOfficeAction() {

        return $this->render('RevizFrontBundle:Front:student.html.twig');
    }
}
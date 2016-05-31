<?php

namespace Reviz\FrontBundle\Controller;

use Doctrine\ORM\Mapping\Id;
use Reviz\FrontBundle\Repository\UserRepository;
use Reviz\FrontBundle\RevizFrontBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller {

    /**
     * @Route("/users", name="indexUsers")
     */
    public function indexAction() {

        $repository = $this->getDoctrine()
            ->getRepository('RevizFrontBundle:User');

        $users = $repository->findAll();
        
        $arrayRoles = array(
            'ROLE_ADMIN'     => 'Administrateur',
            'ROLE_PROFESSOR' => 'Professeur',
            'ROLE_STUDENT'   => 'Etudiant',
            'ROLE_USER'      => 'User'
        );

        return $this->render('RevizFrontBundle:Front:users.html.twig', ['users' => $users, 'roles' => $arrayRoles]);
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
     * @Route("/users/changerole/{id}", name="changerole")
     * @Method({"POST", "GET"})
     */
    public function changeUserRoleAction($id) {

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        $selectOption = $_POST['role'];

        $user->addRole($selectOption);

        $userManager->updateUser($user);

        return $this->redirect('/users');
    }

    /**
     * @Route("/users/removerole/{id}/{role}", name="removerole")
     */
    public function removeUserRole($id, $role) {

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        $roleToRemove = $role;

        $user->removeRole($roleToRemove);

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
<?php

namespace Reviz\FrontBundle\Controller;

use Doctrine\ORM\Mapping\Id;
use Reviz\FrontBundle\Repository\UserRepository;
use Reviz\FrontBundle\RevizFrontBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfToken;

class LoginController extends Controller {

    /**
     * @Route("/admin/users", name="indexUsers")
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
     * @Route("/admin/users/activate/{id}", name="activate")
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

        return $this->redirect('/admin/users');
    }

    /**
     * @Route("/admin/users/changerole/{id}", name="changerole")
     * @Method({"POST", "GET"})
     */
    public function changeUserRoleAction($id, Request $request) {
        
        $session = new Session();
        
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        $selectOption = $request->request->all();

        // todo ajout token

        /*if ($this->get('security.csrf.token_manager')->isTokenValid(new CsrfToken('_token_role', $selectOption['_csrf_token']))) {

            throw new AccessDeniedException('Bad Token');
        }*/

        if (!array_key_exists($selectOption['role'], [
            'ROLE_ADMIN'     => 'Administrateur',
            'ROLE_PROFESSOR' => 'Professeur',
            'ROLE_STUDENT'   => 'Etudiant',
            'ROLE_USER'      => 'User'
            ]
        )) {
            $session->getFlashBag()->add('error_role', 'wrong role given to request');
            return $this->redirect('/admin/users');
        }

        $user->addRole($selectOption['role']);

        $userManager->updateUser($user);

        $session->getFlashBag()->add(
            'notice',
            'coucou coucou coucou !'
        );

        return $this->redirect('/admin/users');
    }

    /**
     * @Route("/admin/users/removerole/{id}/{role}", name="removerole")
     */
    public function removeUserRole($id, $role) {

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        $roleToRemove = $role;

        $user->removeRole($roleToRemove);

        $userManager->updateUser($user);

        return $this->redirect('/admin/users');

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
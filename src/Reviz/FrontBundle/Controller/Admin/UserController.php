<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Reviz\FrontBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * User controller.
 *
 * @Route("/admin/user")
 */
class UserController extends Controller
{

    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('RevizFrontBundle:User')->findAll();

        return $this->render('RevizFrontBundle:Back/User:index.html.twig', [
                'users' => $users,]
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="admin_user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('Reviz\FrontBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $form->getData();
            $user->setPlainPassword($user->getPassword());

            $em->persist($user);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf(
                'success, they are a new user: %s into database ', $user->getUsername()
            ));

            return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render('RevizFrontBundle:Back/User:new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="admin_user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('RevizFrontBundle:Back/User:show.html.twig', [
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\UserType', $user, ['requiredPassword' => false]);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $user = $editForm->getData();

            $user->setPlainPassword($user->getPassword());

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf(
                'success, updated user: %s into database ', $user->getUsername()
            ));

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('RevizFrontBundle:Back/User:edit.html.twig', [
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="admin_user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', ['id' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/command/active", name="user_command_active")
     * @Method("POST")
     */
    public function userCommandActiveAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $id = $request->get('id');
            $em = $this->getDoctrine()->getManager();

            $command = $em->getRepository('RevizFrontBundle:Command')->find((int)$id);
            $user = $command->getUser();

            if (!empty($command)) {
                $isLocked = $command->getIsLocked();
                $command->setIsLocked(!$isLocked);
                $em->persist($command);
                $em->flush();
            }

            $commands = $user->getCommands();

            $data = [];
            foreach ($commands as $command) {
                $active = $command->getIsLocked();
                $module = $command->getTaxonomy();
                $data[] = [
                    'id' => $command->getId(),
                    'name' => $module->getName(),
                    'active' => $active
                ];
            }

            return new Response(json_encode($data));

        }

        return new Response("ok no request ajax");
    }

    /**
     * @Route("/search", name="admin_user_search_modules")
     * @Method("POST")
     */
    public function searchModulesAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $name = $request->get('search');

            $em = $this->getDoctrine()->getManager();

            $modules = $em->getRepository('RevizFrontBundle:Taxonomy')->search($name);

            return new Response(json_encode($modules));

        }

        return new Response("ok no request ajax");
    }
}

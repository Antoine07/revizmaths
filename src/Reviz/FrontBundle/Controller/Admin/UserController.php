<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Reviz\FrontBundle\Entity\User;
use Reviz\FrontBundle\Entity\Command;
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

        return new Response("no request ajax");
    }

    /**
     * @Route("/command/active", name="user_command_active")
     * @Method("POST")
     */
    public function userCommandActiveAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $id = (int)$request->get('id');
            $em = $this->getDoctrine()->getManager();

            $command = $em->getRepository('RevizFrontBundle:Command')->find($id);

            if (is_null($command)) return new Response(json_encode(['message_error' => 'no command exists for you, contact administrator']));

            $user = $command->getUser();

            $isLocked = $command->getIsLocked();

            $status = ($isLocked) ? 'is locked, click here to enabled it?' : 'is not locked, click here to disabled it?';

            $command->setIsLocked(!$isLocked);
            $em->persist($command);
            $em->flush();

            $data = $this->commandByUser($user);
            $data['message_success'] = sprintf('success, %s ', $status);

            return new Response(json_encode($data));

        }

        return new Response("no request ajax");
    }

    /**
     * @Route("/command/delete", name="admin_user_delete_module")
     * @Method("POST")
     */
    public function deleteModuleAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $id = (int)$request->get('id');
            $em = $this->getDoctrine()->getManager();

            $command = $em->getRepository('RevizFrontBundle:Command')->find($id);

            if (is_null($command)) return new Response(json_encode(['message_error' => 'no command exists for you, contact administrator']));

            $user = $command->getUser();

            $em->remove($command);
            $em->flush();

            $data = $this->commandByUser($user);
            $data['message_success'] = 'success delete your command module';

            return new Response(json_encode($data));
        }

        return new Response("no request ajax");
    }

    /**
     * @Route("/command/add", name="admin_user_add_module")
     * @Method("POST")
     */
    public function addModuleAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = [];

            if (
                $request->get('moduleId')
                && $request->get('userId')
            ) {
                $moduleId = (int)$request->get('moduleId');
                $userId = (int)$request->get('userId');
                $em = $this->getDoctrine()->getManager();
                $module = $em->getRepository('RevizFrontBundle:Taxonomy')->find($moduleId);
                $user = $em->getRepository('RevizFrontBundle:User')->find($userId);

                // check if command is not into commands table
                $commands = $em->getRepository('RevizFrontBundle:Command')->findBy([
                    'user' => $userId,
                    'taxonomy' => $moduleId
                ]);

                if (count($commands) > 0) return new Response(json_encode([
                    'message_error' => 'this module is already in your space student'
                ]));

                $command = new Command();
                $command->setTaxonomy($module);
                $command->setUser($user);

                $posts = $module->getPosts();
                $serializedPost = [];
                $serializedVideo = [];
                // posts
                foreach ($posts as $post) {
                    $serializedPost[] = $post->getId();
                    $videos = $this->em->getRepository('RevizFrontBundle:Post')
                        ->getVideos($post->getId());
                    foreach ($videos as $video) {
                        $serializedVideo[] = $video->getId();
                    }
                }
                $command->setAccessPosts(json_encode($serializedPost));
                $command->setAccessVideos(json_encode($serializedVideo));

                $em->persist($command);
                $em->flush();
                $data = $this->commandByUser($user);
                $data['message_success'] = 'success add module in your space student';

                return new Response(json_encode($data));

            } else
                return new Response(json_encode(['message_error' => 'we can not add a new module contact administrator']));
        }

        return new Response("no request ajax");
    }

    /**
     * commandByUser
     *
     * @param User $user
     * @return array
     */
    private function commandByUser(User $user)
    {

        $commands = $user->getCommands();

        $data = [];
        foreach ($commands as $command) {
            $module = $command->getTaxonomy();
            $data[] = [
                'id' => $command->getId(),
                'name' => $module->getName(),
                'active' => $command->getIsLocked()
            ];
        }

        return $data;

    }

}

<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Reviz\FrontBundle\Entity\Level;
use Reviz\FrontBundle\Form\LevelType;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Level controller.
 *
 * @Route("/admin/level")
 */
class LevelController extends Controller
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * Lists all Level entities.
     *
     * @Route("/", name="admin_level_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        return $this->render('RevizFrontBundle:Back/Level:index.html.twig', array(
            'levels' => $levels,
        ));
    }

    /**
     * Creates a new Level entity.
     *
     * @Route("/new", name="admin_level_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $level = new Level();
        $form = $this->createForm('Reviz\FrontBundle\Form\LevelType', $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($level);
            $em->flush();

            return $this->redirectToRoute('admin_level_show', array('id' => $level->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Level:new.html.twig', array(
            'level' => $level,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Level entity.
     *
     * @Route("/{id}", name="admin_level_show")
     * @Method("GET")
     */
    public function showAction(Level $level)
    {
        $deleteForm = $this->createDeleteForm($level);

        return $this->render('RevizFrontBundle:Back/Level:show.html.twig', array(
            'level' => $level,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Level entity.
     *
     * @Route("/{id}/edit", name="admin_level_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Level $level)
    {
        $deleteForm = $this->createDeleteForm($level);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\LevelType', $level);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($level);
            $em->flush();

            return $this->redirectToRoute('admin_level_edit', array('id' => $level->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Level:edit.html.twig', array(
            'level' => $level,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Level entity.
     *
     * @Route("/{id}", name="admin_level_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Level $level)
    {
        $form = $this->createDeleteForm($level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($level);
            $em->flush();
        }

        return $this->redirectToRoute('admin_level_index');
    }

    /**
     * Creates a form to delete a Level entity.
     *
     * @param Level $level The Level entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Level $level)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_level_delete', array('id' => $level->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

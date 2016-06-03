<?php

namespace Reviz\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Reviz\FrontBundle\Entity\Exercice;
use Reviz\FrontBundle\Form\ExerciceType;

/**
 * Exercice controller.
 *
 * @Route("/admin/exercice")
 */
class ExerciceController extends Controller
{
    /**
     * Lists all Exercice entities.
     *
     * @Route("/", name="admin_exercice_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $exercices = $em->getRepository('RevizFrontBundle:Exercice')->findAll();

        return $this->render('exercice/index.html.twig', array(
            'exercices' => $exercices,
        ));
    }

    /**
     * Creates a new Exercice entity.
     *
     * @Route("/new", name="admin_exercice_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $exercice = new Exercice();
        $form = $this->createForm('Reviz\FrontBundle\Form\ExerciceType', $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exercice);
            $em->flush();

            return $this->redirectToRoute('admin_exercice_show', array('id' => $exercice->getId()));
        }

        return $this->render('exercice/new.html.twig', array(
            'exercice' => $exercice,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Exercice entity.
     *
     * @Route("/{id}", name="admin_exercice_show")
     * @Method("GET")
     */
    public function showAction(Exercice $exercice)
    {
        $deleteForm = $this->createDeleteForm($exercice);

        return $this->render('exercice/show.html.twig', array(
            'exercice' => $exercice,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Exercice entity.
     *
     * @Route("/{id}/edit", name="admin_exercice_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Exercice $exercice)
    {
        $deleteForm = $this->createDeleteForm($exercice);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\ExerciceType', $exercice);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exercice);
            $em->flush();

            return $this->redirectToRoute('admin_exercice_edit', array('id' => $exercice->getId()));
        }

        return $this->render('exercice/edit.html.twig', array(
            'exercice' => $exercice,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Exercice entity.
     *
     * @Route("/{id}", name="admin_exercice_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Exercice $exercice)
    {
        $form = $this->createDeleteForm($exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($exercice);
            $em->flush();
        }

        return $this->redirectToRoute('admin_exercice_index');
    }

    /**
     * Creates a form to delete a Exercice entity.
     *
     * @param Exercice $exercice The Exercice entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Exercice $exercice)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_exercice_delete', array('id' => $exercice->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

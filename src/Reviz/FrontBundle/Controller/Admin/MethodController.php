<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Reviz\FrontBundle\Entity\Method as RevizMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Method controller.
 *
 * @Route("/admin/method")
 */
class MethodController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Lists all Method entities.
     *
     * @Route("/", name="admin_method_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $methods = $em->getRepository('RevizFrontBundle:Method')->findAll();

        return $this->render('RevizFrontBundle:Back/Method:index.html.twig', array(
            'methods' => $methods,
        ));
    }

    /**
     * Creates a new Method entity.
     *
     * @Route("/new", name="admin_method_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $method = new RevizMethod();
        $form = $this->createForm('Reviz\FrontBundle\Form\MethodType', $method);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($method);
            $em->flush();

            return $this->redirectToRoute('admin_method_show', array('id' => $method->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Method:new.html.twig', array(
            'method' => $method,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Method entity.
     *
     * @Route("/{id}", name="admin_method_show")
     * @Method("GET")
     */
    public function showAction(RevizMethod $method)
    {
        $deleteForm = $this->createDeleteForm($method);

        return $this->render('RevizFrontBundle:Back/Method:show.html.twig', array(
            'method' => $method,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Method entity.
     *
     * @Route("/{id}/edit", name="admin_method_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, RevizMethod $method)
    {
        $deleteForm = $this->createDeleteForm($method);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\MethodType', $method);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($method);
            $em->flush();

            return $this->redirectToRoute('admin_method_edit', array('id' => $method->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Method:edit.html.twig', array(
            'method' => $method,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Method entity.
     *
     * @Route("/{id}", name="admin_method_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MRevizMethod $method)
    {
        $form = $this->createDeleteForm($method);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($method);
            $em->flush();
        }

        return $this->redirectToRoute('admin_method_index');
    }

    /**
     * Creates a form to delete a Method entity.
     *
     * @param Method $method The Method entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RevizMethod $method)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_method_delete', array('id' => $method->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

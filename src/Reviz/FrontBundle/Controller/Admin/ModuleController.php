<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Reviz\FrontBundle\Entity\Module;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Module controller.
 *
 * @Route("/admin/module")
 */
class ModuleController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Lists all Module entities.
     *
     * @Route("/", name="admin_module_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();

        return $this->render('RevizFrontBundle:Back/Module:index.html.twig', array(
            'modules' => $modules,
        ));
    }

    /**
     * Creates a new Module entity.
     *
     * @Route("/new", name="admin_module_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $module = new Module();
        $form = $this->createForm('Reviz\FrontBundle\Form\ModuleType', $module);
<<<<<<< 29ccd32116e0f660c19a8c521cc23909af8318c1

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $levelsForm = $this->createFormBuilder($levels)
              ->add('level', ChoiceType::class, array(
                    'choices' => $levelsFields
              ))
              ->getForm()
        ;

        $form->handleRequest($request);
        $levelsForm->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $module = $form->getData();
            $level = $levelsForm->getData();

            if (isset($level['level'])) {

                $module->setParentId($level['level']);
            }

=======
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
>>>>>>> Debut CRUD method
            $em->persist($module);
            $em->flush();

            return $this->redirectToRoute('admin_module_show', array('id' => $module->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Module:new.html.twig', array(
            'module' => $module,
<<<<<<< 29ccd32116e0f660c19a8c521cc23909af8318c1
            'level_form' => $levelsForm->createView(),
=======
>>>>>>> Debut CRUD method
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Module entity.
     *
     * @Route("/{id}", name="admin_module_show")
     * @Method("GET")
     */
    public function showAction(Module $module)
    {
        $deleteForm = $this->createDeleteForm($module);

        return $this->render('RevizFrontBundle:Back/Module:show.html.twig', array(
            'module' => $module,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Module entity.
     *
     * @Route("/{id}/edit", name="admin_module_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Module $module)
    {
        $deleteForm = $this->createDeleteForm($module);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\ModuleType', $module);

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $levelsForm = $this->createFormBuilder($levels)
              ->add('level', ChoiceType::class, array(
                    'choices' => $levelsFields
              ))
              ->getForm()
        ;

        $editForm->handleRequest($request);
        $levelsForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $module = $editForm->getData();
            $level = $levelsForm->getData();

            if (isset($level['level'])) {

                // reset relation table post_taxonomy because must be unique
                $module->setParentId($level['level']);
            }

            $em->persist($module);
            $em->flush();

            return $this->redirectToRoute('admin_module_edit', array('id' => $module->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Module:edit.html.twig', array(
            'module' => $module,
            'level_form' => $levelsForm->createView(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Module entity.
     *
     * @Route("/{id}", name="admin_module_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Module $module)
    {
        $form = $this->createDeleteForm($module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($module);
            $em->flush();
        }

        return $this->redirectToRoute('admin_module_index');
    }

    /**
     * Creates a form to delete a Module entity.
     *
     * @param Module $module The Module entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Module $module)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_module_delete', array('id' => $module->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

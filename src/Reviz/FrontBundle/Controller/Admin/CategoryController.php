<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Reviz\FrontBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Category controller.
 *
 * @Route("/admin/category")
 */
class CategoryController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="admin_category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('RevizFrontBundle:Category')->findAll();

        return $this->render('RevizFrontBundle:Back/Category:index.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/new", name="admin_category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $newForm = $this->createForm('Reviz\FrontBundle\Form\CategoryType', $category);

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();
        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $modulesFields = [];
        foreach ($modules as $module) $modulesFields[$module->getName()] = $module->getId();

        $levelsForm = $this->createFormBuilder($levels)
              ->add('level', ChoiceType::class, array(
                    'choices' => $levelsFields
              ))
              ->getForm();

        $modulesForm = $this->createFormBuilder($modules)
              ->add('module', ChoiceType::class, array(
                    'choices' => $modulesFields
              ))
              ->getForm();

        $newForm->handleRequest($request);
        $levelsForm->handleRequest($request);
        $modulesForm->handleRequest($request);

        if ($newForm->isSubmitted() && $newForm->isValid()) {

            $category = $newForm->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();

            if (isset($level['level']) && isset($module['module'])) {

                // reset relation table post_taxonomy because must be unique
                foreach ($levels as $reset);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                          'this relation must be correct with parent id, check module %s if parent with the level %s',
                          $moduleEntity[0]->getName(),
                          $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_category_edit', array('id' => $category->getId()));
                }

                $category->setParentId($module['module']);
            }

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_category_show', array('id' => $category->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Category:new.html.twig', array(
            'category' => $category,
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'new_form' => $newForm->createView(),
        ));
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="admin_category_show")
     * @Method("GET")
     */
    public function showAction(Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('RevizFrontBundle:Back/Category:show.html.twig', array(
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="admin_category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\CategoryType', $category);

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();
        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $modulesFields = [];
        foreach ($modules as $module) $modulesFields[$module->getName()] = $module->getId();

        $levelsForm = $this->createFormBuilder($levels)
              ->add('level', ChoiceType::class, array(
                    'choices' => $levelsFields
              ))
              ->getForm();

        $modulesForm = $this->createFormBuilder($modules)
              ->add('module', ChoiceType::class, array(
                    'choices' => $modulesFields
              ))
              ->getForm();

        $editForm->handleRequest($request);
        $levelsForm->handleRequest($request);
        $modulesForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $category = $editForm->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();

            if (isset($level['level']) && isset($module['module'])) {

                // reset relation table post_taxonomy because must be unique
                foreach ($levels as $reset);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                          'this relation must be correct with parent id, check module %s if parent with the level %s',
                          $moduleEntity[0]->getName(),
                          $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_category_edit', array('id' => $category->getId()));
                }

                $category->setParentId($module['module']);
            }

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_category_edit', array('id' => $category->getId()));
        }

        return $this->render('RevizFrontBundle:Back/Category:edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="admin_category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('admin_category_index');
    }

    /**
     * Creates a form to delete a Category entity.
     *
     * @param Category $category The Category entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_category_delete', array('id' => $category->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

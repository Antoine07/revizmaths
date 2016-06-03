<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        return $this->render('RevizFrontBundle:Back:Exercice/index.html.twig', array(
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

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();
        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();
        $categories = $em->getRepository('RevizFrontBundle:Category')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $modulesFields = [];
        foreach ($modules as $module) $modulesFields[$module->getName()] = $module->getId();

        $categoriesFields = [];
        foreach ($categories as $category) $categoriesFields[$category->getName()] = $category->getId();

        $levelsForm = $this->createFormBuilder()
            ->add('level', ChoiceType::class, array(
                'choices' => $levelsFields,
            ))
            ->getForm();

        $modulesForm = $this->createFormBuilder()
            ->add('module', ChoiceType::class, array(
                'choices' => $modulesFields,
            ))
            ->getForm();

        $categoriesForm = $this->createFormBuilder()
            ->add('category', ChoiceType::class, array(
                'choices' => $categoriesFields,
            ))
            ->getForm();

        $form->handleRequest($request);
        $levelsForm->handleRequest($request);
        $modulesForm->handleRequest($request);
        $categoriesForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercice = $form->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();
            $category = $categoriesForm->getData();

            if (isset($level['level']) && isset($module['module']) && isset($category['category'])) {

                // reset relation table post_taxonomy because must be unique and the relation is many to many
                foreach ($levels as $resetLevel) $exercice->removeTaxonomy($resetLevel);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $exercice->addTaxonomy($levelEntity[0]);

                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check module %s if parent with the level %s',
                        $moduleEntity[0]->getName(),
                        $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $exercice->getId()));
                }

                $categoryEntity = $em->getRepository('RevizFrontBundle:Category')->findById((int)$category['category']);
                // check if category is in its own category
                if ($module['module'] != $categoryEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check category %s if parent with the module %s',
                        $categoryEntity[0]->getName(),
                        $moduleEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $exercice->getId()));
                }

                // module
                foreach ($modules as $resetModule) $exercice->removeTaxonomy($resetModule);
                $exercice->addTaxonomy($moduleEntity[0]);

                // category
                foreach ($categories as $resetCategory) $exercice->removeTaxonomy($resetCategory);
                $exercice->addTaxonomy($categoryEntity[0]);

            }

            $em->persist($exercice);
            $em->flush();

            return $this->redirectToRoute('admin_exercice_show', array('id' => $exercice->getId()));
        }

        return $this->render('RevizFrontBundle:Back:Exercice/new.html.twig', array(
            'exercice' => $exercice,
            'new_form' => $form->createView(),
            'levels' => $levels,
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'category_form' => $categoriesForm->createView(),
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

        return $this->render('RevizFrontBundle:Back:Exercice/show.html.twig', array(
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

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();
        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();
        $categories = $em->getRepository('RevizFrontBundle:Category')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $modulesFields = [];
        foreach ($modules as $module) $modulesFields[$module->getName()] = $module->getId();

        $categoriesFields = [];
        foreach ($categories as $category) $categoriesFields[$category->getName()] = $category->getId();

        $levelsForm = $this->createFormBuilder()
            ->add('level', ChoiceType::class, array(
                'choices' => $levelsFields,
            ))
            ->getForm();

        $modulesForm = $this->createFormBuilder()
            ->add('module', ChoiceType::class, array(
                'choices' => $modulesFields,
            ))
            ->getForm();

        $categoriesForm = $this->createFormBuilder()
            ->add('category', ChoiceType::class, array(
                'choices' => $categoriesFields,
            ))
            ->getForm();

        $editForm->handleRequest($request);
        $levelsForm->handleRequest($request);
        $modulesForm->handleRequest($request);
        $categoriesForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $course = $editForm->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();
            $category = $categoriesForm->getData();

            if (isset($level['level']) && isset($module['module']) && isset($category['category'])) {

                // reset relation table post_taxonomy because must be unique and the relation is many to many
                foreach ($levels as $resetLevel) $course->removeTaxonomy($resetLevel);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $exercice->addTaxonomy($levelEntity[0]);

                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check module %s if parent with the level %s',
                        $moduleEntity[0]->getName(),
                        $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $exercice->getId()));
                }

                $categoryEntity = $em->getRepository('RevizFrontBundle:Category')->findById((int)$category['category']);
                // check if category is in its own category
                if ($module['module'] != $categoryEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check category %s if parent with the module %s',
                        $categoryEntity[0]->getName(),
                        $moduleEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $exercice->getId()));
                }

                // module
                foreach ($modules as $resetModule) $exercice->removeTaxonomy($resetModule);
                $exercice->addTaxonomy($moduleEntity[0]);

                // category
                foreach ($categories as $resetCategory) $exercice->removeTaxonomy($resetCategory);
                $exercice->addTaxonomy($categoryEntity[0]);

            }

            $em->persist($exercice);
            $em->flush();

            return $this->redirectToRoute('admin_exercice_edit', array('id' => $exercice->getId()));
        }

        return $this->render('RevizFrontBundle:Back:Exercice/edit.html.twig', array(
            'exercice' => $exercice,
            'edit_form' => $editForm->createView(),
            'levels' => $levels,
            'modules' => $modules,
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'category_form' => $categoriesForm->createView(),
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

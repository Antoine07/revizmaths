<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Reviz\FrontBundle\Entity\Answer;
use Reviz\FrontBundle\Form\AnswerType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Answer controller.
 *
 * @Route("/admin/answer")
 */
class AnswerController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Lists all Answer entities.
     *
     * @Route("/", name="admin_answer_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $answers = $em->getRepository('RevizFrontBundle:Answer')->findAll();

        return $this->render('RevizFrontBundle:Back:Answer/index.html.twig', array(
            'answers' => $answers,
        ));
    }

    /**
     * Creates a new Answer entity.
     *
     * @Route("/new", name="admin_answer_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $answer = new Answer();
        $form = $this->createForm('Reviz\FrontBundle\Form\AnswerType', $answer);

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();
        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();
        $categories = $em->getRepository('RevizFrontBundle:Category')->findAll();
        $exercices = $em->getRepository('RevizFrontBundle:Exercice')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $modulesFields = [];
        foreach ($modules as $module) $modulesFields[$module->getName()] = $module->getId();

        $categoriesFields = [];
        foreach ($categories as $category) $categoriesFields[$category->getName()] = $category->getId();

        $exercicesFields = [];
        foreach ($exercices as $exercice) $exercicesFields[$exercice->getTitle()] = $exercice->getId();

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

        $exercicesForm = $this->createFormBuilder()
            ->add('exercices', ChoiceType::class, array(
                'choices' => $exercicesFields,
            ))
            ->getForm();

        $form->handleRequest($request);
        $levelsForm->handleRequest($request);
        $modulesForm->handleRequest($request);
        $categoriesForm->handleRequest($request);
        $exercicesForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer = $form->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();
            $category = $categoriesForm->getData();
            $exercice = $exercicesForm->getData();

            if (isset($level['level']) && isset($module['module']) && isset($category['category']) && isset($exercice['exercices'])) {

                // reset relation table post_taxonomy because must be unique and the relation is many to many
                foreach ($levels as $resetLevel) $answer->removeTaxonomy($resetLevel);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $answer->addTaxonomy($levelEntity[0]);

                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check module %s if parent with the level %s',
                        $moduleEntity[0]->getName(),
                        $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_answer_new', array('id' => $answer->getId()));
                }

                $categoryEntity = $em->getRepository('RevizFrontBundle:Category')->findById((int)$category['category']);
                // check if category is in its own category
                if ($module['module'] != $categoryEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check category %s if parent with the module %s',
                        $categoryEntity[0]->getName(),
                        $moduleEntity[0]->getName()
                    ));
                    return $this->redirectToRoute('admin_answer_new', array('id' => $answer->getId()));
                }

                //level
                //$a = $level->getNbAnswer() + 1;
                //$level->setNbAnswer($a);

                //var_dump($level['level']->getNbAnswer()); die;

                // module
                foreach ($modules as $resetModule) $answer->removeTaxonomy($resetModule);
                $answer->addTaxonomy($moduleEntity[0]);

                // category
                foreach ($categories as $resetCategory) $answer->removeTaxonomy($resetCategory);
                $answer->addTaxonomy($categoryEntity[0]);

                //exercice
                $answer->setPostParent($exercice['exercices']);
            }

            $em->persist($answer);
            $em->flush();

            return $this->redirectToRoute('admin_answer_show', array('id' => $answer->getId()));
        }

        return $this->render('RevizFrontBundle:Back:Answer/new.html.twig', array(
            'answer' => $answer,
            'new_form' => $form->createView(),
            'levels' => $levels,
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'category_form' => $categoriesForm->createView(),
            'exercice_form' => $exercicesForm->createView(),
        ));
    }

    /**
     * Finds and displays a Answer entity.
     *
     * @Route("/{id}", name="admin_answer_show")
     * @Method("GET")
     */
    public function showAction(Answer $answer)
    {
        $deleteForm = $this->createDeleteForm($answer);

        return $this->render('RevizFrontBundle:Back:Answer/show.html.twig', array(
            'answer' => $answer,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Answer entity.
     *
     * @Route("/{id}/edit", name="admin_answer_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Answer $answer)
    {
        $deleteForm = $this->createDeleteForm($answer);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\AnswerType', $answer);

        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('RevizFrontBundle:Level')->findAll();
        $modules = $em->getRepository('RevizFrontBundle:Module')->findAll();
        $categories = $em->getRepository('RevizFrontBundle:Category')->findAll();
        $exercices = $em->getRepository('RevizFrontBundle:Exercice')->findAll();

        $levelsFields = [];
        foreach ($levels as $level) $levelsFields[$level->getName()] = $level->getId();

        $modulesFields = [];
        foreach ($modules as $module) $modulesFields[$module->getName()] = $module->getId();

        $categoriesFields = [];
        foreach ($categories as $category) $categoriesFields[$category->getName()] = $category->getId();

        $exercicesFields = [];
        foreach ($exercices as $exercice) $exercicesFields[$exercice->getTitle()] = $exercice->getId();

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

        $exercicesForm = $this->createFormBuilder()
            ->add('exercices', ChoiceType::class, array(
                'choices' => $exercicesFields,
            ))
            ->getForm();

        $editForm->handleRequest($request);
        $levelsForm->handleRequest($request);
        $modulesForm->handleRequest($request);
        $categoriesForm->handleRequest($request);
        $exercicesForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $answer = $editForm->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();
            $category = $categoriesForm->getData();
            $exercice = $exercicesForm->getData();

            if (isset($level['level']) && isset($module['module']) && isset($category['category']) && isset($exercice['exercices'])) {

                // reset relation table post_taxonomy because must be unique and the relation is many to many
                foreach ($levels as $resetLevel) $answer->removeTaxonomy($resetLevel);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $answer->addTaxonomy($levelEntity[0]);

                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check module %s if parent with the level %s',
                        $moduleEntity[0]->getName(),
                        $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_answer_new', array('id' => $answer->getId()));
                }

                $categoryEntity = $em->getRepository('RevizFrontBundle:Category')->findById((int)$category['category']);
                // check if category is in its own category
                if ($module['module'] != $categoryEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check category %s if parent with the module %s',
                        $categoryEntity[0]->getName(),
                        $moduleEntity[0]->getName()
                    ));
                    return $this->redirectToRoute('admin_answer_new', array('id' => $answer->getId()));
                }

                //level
                //$a = $level->getNbAnswer() + 1;
                //$level->setNbAnswer($a);

                //var_dump($level['level']->getNbAnswer()); die;

                // module
                foreach ($modules as $resetModule) $answer->removeTaxonomy($resetModule);
                $answer->addTaxonomy($moduleEntity[0]);

                // category
                foreach ($categories as $resetCategory) $answer->removeTaxonomy($resetCategory);
                $answer->addTaxonomy($categoryEntity[0]);

                //exercice
                $answer->setPostParent($exercice['exercices']);
            }

            $em->persist($answer);
            $em->flush();

            return $this->redirectToRoute('admin_answer_edit', array('id' => $answer->getId()));
        }

        return $this->render('RevizFrontBundle:Back:Answer/edit.html.twig', array(
            'answer' => $answer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'levels' => $levels,
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'category_form' => $categoriesForm->createView(),
            'exercice_form' => $exercicesForm->createView(),
        ));
    }

    /**
     * Deletes a Answer entity.
     *
     * @Route("/{id}", name="admin_answer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Answer $answer)
    {
        $form = $this->createDeleteForm($answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($answer);
            $em->flush();
        }

        return $this->redirectToRoute('admin_answer_index');
    }

    /**
     * Creates a form to delete a Answer entity.
     *
     * @param Answer $answer The Answer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Answer $answer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_answer_delete', array('id' => $answer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

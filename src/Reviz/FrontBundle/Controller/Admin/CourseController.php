<?php

namespace Reviz\FrontBundle\Controller\Admin;

use Reviz\FrontBundle\Entity\Course;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Course controller.
 *
 * @Route("/admin/course")
 */
class CourseController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Lists all Course entities.
     *
     * @Route("/", name="admin_course_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $courses = $em->getRepository('RevizFrontBundle:Course')->findAll();

        dump($courses);

        return $this->render('RevizFrontBundle:Back:Course/index.html.twig', array(
            'courses' => $courses,
        ));
    }

    /**
     * Creates a new Course entity.
     *
     * @Route("/new", name="admin_course_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $course = new Course();
        $form = $this->createForm('Reviz\FrontBundle\Form\CourseType', $course);

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
            $course = $form->getData();
            $level = $levelsForm->getData();
            $module = $modulesForm->getData();
            $category = $categoriesForm->getData();

            if (isset($level['level']) && isset($module['module']) && isset($category['category'])) {

                // reset relation table post_taxonomy because must be unique and the relation is many to many
                foreach ($levels as $resetLevel) $course->removeTaxonomy($resetLevel);

                $levelEntity = $em->getRepository('RevizFrontBundle:Level')->findById((int)$level['level']);
                $course->addTaxonomy($levelEntity[0]);

                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check module %s if parent with the level %s',
                        $moduleEntity[0]->getName(),
                        $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $course->getId()));
                }

                $categoryEntity = $em->getRepository('RevizFrontBundle:Category')->findById((int)$category['category']);
                // check if category is in its own category
                if ($module['module'] != $categoryEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check category %s if parent with the module %s',
                        $categoryEntity[0]->getName(),
                        $moduleEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $course->getId()));
                }

                // module
                foreach ($modules as $resetModule) $course->removeTaxonomy($resetModule);
                $course->addTaxonomy($moduleEntity[0]);

                // category
                foreach ($categories as $resetCategory) $course->removeTaxonomy($resetCategory);
                $course->addTaxonomy($categoryEntity[0]);

            }

            $em->persist($course);
            $em->flush();

            return $this->redirectToRoute('admin_course_show', array('id' => $course->getId()));
        }

        return $this->render('RevizFrontBundle:Back:Course/new.html.twig', array(
            'course' => $course,
            'new_form' => $form->createView(),
            'levels' => $levels,
            'level_form' => $levelsForm->createView(),
            'module_form' => $modulesForm->createView(),
            'category_form' => $categoriesForm->createView(),
        ));
    }

    /**
     * Finds and displays a Course entity.
     *
     * @Route("/{id}", name="admin_course_show")
     * @Method("GET")
     */
    public function showAction(Course $course)
    {
        $deleteForm = $this->createDeleteForm($course);

        return $this->render('RevizFrontBundle:Back:Course/show.html.twig', array(
            'course' => $course,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Course entity.
     *
     * @Route("/{id}/edit", name="admin_course_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Course $course)
    {
        $deleteForm = $this->createDeleteForm($course);
        $editForm = $this->createForm('Reviz\FrontBundle\Form\CourseType', $course);

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
                $course->addTaxonomy($levelEntity[0]);

                $moduleEntity = $em->getRepository('RevizFrontBundle:Module')->findById((int)$module['module']);

                // check if level is parent of module nothing is wrong back with session flash message
                if ($level['level'] != $moduleEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check module %s if parent with the level %s',
                        $moduleEntity[0]->getName(),
                        $levelEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $course->getId()));
                }

                $categoryEntity = $em->getRepository('RevizFrontBundle:Category')->findById((int)$category['category']);
                // check if category is in its own category
                if ($module['module'] != $categoryEntity[0]->getParentId()) {

                    $this->session->getFlashBag()->add('warning', sprintf(
                        'this relation must be correct with parent id, check category %s if parent with the module %s',
                        $categoryEntity[0]->getName(),
                        $moduleEntity[0]->getName()
                    ));

                    return $this->redirectToRoute('admin_course_edit', array('id' => $course->getId()));
                }

                // module
                foreach ($modules as $resetModule) $course->removeTaxonomy($resetModule);
                $course->addTaxonomy($moduleEntity[0]);

                // category
                foreach ($categories as $resetCategory) $course->removeTaxonomy($resetCategory);
                $course->addTaxonomy($categoryEntity[0]);

            }

            $em->persist($course);
            $em->flush();

            return $this->redirectToRoute('admin_course_edit', ['id' => $course->getId()]);
        }

        return $this->render('RevizFrontBundle:Back:Course/edit.html.twig', array(
            'course' => $course,
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
     * Deletes a Course entity.
     *
     * @Route("/{id}", name="admin_course_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Course $course)
    {
        $form = $this->createDeleteForm($course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($course);
            $em->flush();
        }

        return $this->redirectToRoute('admin_course_index');
    }

    /**
     * Creates a form to delete a Course entity.
     *
     * @param Course $course The Course entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Course $course)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_course_delete', array('id' => $course->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}

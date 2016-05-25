<?php

namespace Reviz\FrontBundle\Controller;

use Reviz\FrontBundle\Entity\Category;
use Reviz\FrontBundle\Entity\Comment;
use Reviz\FrontBundle\Entity\Course;
use Reviz\FrontBundle\Entity\Exercice;
use Reviz\FrontBundle\Entity\Answer;
use Reviz\FrontBundle\Entity\Field;
use Reviz\FrontBundle\Entity\Form;
use Reviz\FrontBundle\Entity\Image;
use Reviz\FrontBundle\Entity\Method as M;
use Reviz\FrontBundle\Entity\Tag;
use Reviz\FrontBundle\Entity\User;
use Reviz\FrontBundle\Entity\Userdata;
use Reviz\FrontBundle\Form\ExerciceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $geometry = new Category();
        $geometry->setName('Géométrie suite');

        $answer = new Answer();
        $answer->setTitle("corrigé de l'exercice sur les suites géométriques");
        $answer->setContent("pourquoi ce contenu est obligatoire");
        $answer->addTaxonomy($geometry);

        $exo = new Exercice();
        $exo->setTitle("corrigé de l'exercice sur les suites géométriques");
        $exo->setContent("pourquoi ce contenu est obligatoire");
        $exo->setStatus('published');
        $exo->addTaxonomy($geometry);

        $method = new M();

        $method->setTitle("une méthode sur les résolutions des suites");
        $method->setContent("blabla");


        $course = new Course();
        $course->setTitle("intégration");
        $course->setContent("intégration par parties");


        $image = new Image();

        $image->setUrl('/mon_image.jpg');
        $image->setName('mon image');

        $user = new User();

        $user->setUsername('Antoine');
        $user->setPassword('Antoine');
        $user->setEmail('antoine' . rand(1, 100) . '.lucsko@gmail.com');

        $user->setMedia($image);

        $em->persist($answer);
        $em->persist($exo);
        $em->persist($method);
        $em->persist($course);
        $em->persist($user);
        $em->persist($geometry);

        $em->flush();

        dump($this->get('reviz.antispam'));

        return $this->render('RevizFrontBundle:Default:index.html.twig');
    }

    /**
     * @Route("/test")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction()
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('RevizFrontBundle:User')->find(1);
        $image = $user->getMedia();
        var_dump($user->getUsername());
        var_dump($image->getUrl());

        $course = new Course;
        $course->setTitle("homothétie");
        $course->setContent("homothétie géométrie dans l'espace");

        $comment = new Comment;
        $comment->setTitle('super cours!');
        $comment->setEmail('antoine.l');
        $comment->setContent('bonjour je suis intéressé par des cours de maths');

        $comment2 = new Comment;
        $comment2->setTitle('super cours!');
        $comment2->setEmail('antoine.l');
        $comment2->setContent('bonjour je suis intéressé par des cours de maths');

        $comment->setCourse($course);
        $comment2->setCourse($course);

        $em = $this->getDoctrine()->getManager();

        $em->persist($course);
        $em->persist($comment);
        $em->persist($comment2);

        $em->flush();

        return $this->render('RevizFrontBundle:Default:index.html.twig');
    }

    /**
     * @Route("/relation")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relationAction()
    {
        $tags = [
            'foo',
            'bar',
            'baz',
            'boo'
        ];

        $em = $this->getDoctrine()->getManager();

        $course = $em->getRepository('RevizFrontBundle:Post')->find(2);

        foreach ($tags as $name) {
            $tag = new Tag;
            $tag->setName($name);
            $em->persist($tag);
        }
        $em->flush();

        return $this->render('RevizFrontBundle:Default:index.html.twig');
    }

    /**
     * @Route("pull")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pullAction()
    {

        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('RevizFrontBundle:Tag')->findAll();

        $course = $em->getRepository('RevizFrontBundle:Post')->find(1);

        foreach ($tags as $tag)
            $course->addTaxonomy($tag);

        $em->flush();

        return $this->render('RevizFrontBundle:Default:index.html.twig');

    }

    /**
     * @Route("show")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();

        $course = $em->getRepository('RevizFrontBundle:Course')->find(1);

        return $this->render('RevizFrontBundle:Default:tag.html.twig', compact('course'));
    }

    /**
     * @Route("comments")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function commentsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $course = new Course;
        $course->setTitle("géométrie dans l'espace chap2");
        $course->setContent("géométrie dans l'espace avec raspberry");

        $comment = new Comment;
        $comment->setTitle('super cours bis sur la géométrie!');
        $comment->setEmail('antoine.lll');
        $comment->setContent('bonjour je suis intéressé par des cours de maths');

        $course->addComment($comment);

        $em->persist($comment);
        $em->persist($course);

        $em->flush();


        return $this->render('RevizFrontBundle:Default:index.html.twig');
    }

    /**
     * @Route("user")
     *
     */
    public function userAction()
    {

        $user = new User;

        $user->setUsername('Tony');
        $user->setEmail('tony@tony;fr');
        $user->setPassword('Tony');

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);

        $course = new Course;
        $course->setTitle('arithmétique RSA');
        $course->setContent('blabla');

        $course->setUser($user);

        $em->persist($course);

        $em->flush();

        return $this->render('RevizFrontBundle:Default:user.html.twig');

    }

    /**
     * @Route("user/{id}")
     */
    public function showUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $courses = $em
            ->getRepository('RevizFrontBundle:Post')
            ->findByUserId($request->get('id'));
        // ByUserId __call($name, $args)  user_id = 3

        return $this->render(
            'RevizFrontBundle:Default:show.html.twig',
            compact('courses')
        );

    }

    /**
     * @Route("createcomments")
     */
    public function createcommentsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $course = new Course;
        $course->setTitle("graphe orienté");
        $course->setContent("orienté graphe partitionné");

        $comments = [
            [
                'title' => 'boo',
                'email' => 'tony@tony.fr' . rand(1, 10000),
                'content' => 'blabla blabla',
            ],
            [
                'title' => 'barr',
                'email' => 'tony@tony.fr' . rand(1, 10000),
                'content' => 'blabla blabla',
            ],
            [
                'title' => 'bryyy',
                'email' => 'tony@tony.fr' . rand(1, 10000),
                'content' => 'blabla blabla',
            ],
            [
                'title' => 'fooo',
                'email' => 'tony@tony.fr' . rand(1, 10000),
                'content' => 'blabla blabla',
            ],
        ];

        foreach ($comments as $co) {
            $comment = new Comment;
            $comment->setTitle($co['title']);
            $comment->setEmail($co['email']);
            $comment->setContent($co['content']);

            $course->addComment($comment);

            $em->persist($comment);
            $em->persist($course);

        }

        $em->flush();

        return $this->render('RevizFrontBundle:Default:user.html.twig');

    }

    /**
     * @Route("showcomments")
     */
    public function showCommentsByCourse()
    {
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('RevizFrontBundle:Course')->find(13);

        return $this->render(
            'RevizFrontBundle:Default:comments.html.twig',
            compact('course')
        );
    }

    /**
     * @Route("remove")
     */
    public function removeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('RevizFrontBundle:Course')->find(13);

        $em->remove($course);
        $em->flush();
    }


    /**
     * @Route("createuserdata")
     */
    public function createUserdataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User;
        $user->setEmail('foo@foo.fr' . rand(1, 10000));
        $user->setPassword('foo');
        $user->setUsername('foo');
        $userdata = new Userdata();
        $userdata->setConfig(json_encode(['color', 'rate', 'avatar']));

        $user->setUserdata($userdata);

        $em->persist($user);

        $em->flush();
    }

    /**
     * @Route("removeuserdata")
     */
    public function removeUserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('RevizFrontBundle:User')->find(12);

        $em->remove($user);
        $em->flush();
    }

    /**
     * @Route("createform")
     */
    public function createFormAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = new Form();
        $form->setName('foo');
        $form->setTitle('foo');
        $form->setAction('foo');
        $form->setDescription('lklklklakzlk');

        foreach (range(1, 10) as $r) {
            $field = new Field();
            $field->setName('foo');
            $field->setType('text');
            $field->setData('blabla');
            $form->addField($field);
        }

        $em->persist($form);

        $em->flush();
    }

    /**
     * @Route("modules")
     */
    public function showModule()
    {
        $em = $this->getDoctrine()->getManager();
        $title = "Les modules de la 6eme";
        $modules = $em->getRepository('RevizFrontBundle:Taxonomy')->allModulesByLevel('6 eme');

        var_dump($modules);

        return $this->render(
            'RevizFrontBundle:Default:module.html.twig',
            compact('modules', 'title')
        );
    }

    /**
     * @Route("exception")
     */
    public function showException()
    {
        throw $this->createNotFoundException('The product does not exist');
    }

    /**
     * @Route("form", name="form")
     * @Method({"GET", "POST"})
     */
    public function showForm(Request $request)
    {
        $exercice = new Exercice;
        $form = $this->createForm(ExerciceType::class, $exercice);

        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
                throw new AccessDeniedException('accès limit au administrateur du site');

            dump($request->get('title'));
            dump($form->getClickedButton()->getName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($exercice);
            $em->flush();

            $request->getSession()->getFlashBag()->add('exercice', 'exercice ajouté');

            return $this->redirect($this->generateUrl('showexercice', ['id' => $exercice->getId()]));

        }

        $form = $form->createView();

        return $this->render(
            'RevizFrontBundle:Default:form.html.twig',
            compact('form')
        );
    }

    /**
     * @Route("simpleform", name="simpleform")
     * @Method({"GET", "POST"})
     */
    public function creatSimpleFormAction()
    {
        $form = $this->createFormBuilder()
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        return $this->render('RevizFrontBundle:Default:simpleform.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("showexercice/{id}", name="showexercice")
     * @Method({"GET"})
     */
    public function showExercice($id)
    {
        $em = $this->getDoctrine()->getManager();
        $exercices = $em->getRepository('RevizFrontBundle:Exercice')->findAll();

        return $this->render('RevizFrontBundle:Default:showexercice.html.twig', compact('exercices', 'id'));
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 24/05/2016
 * Time: 11:42
 */

namespace Test\FrontBundle;

use Reviz\FrontBundle\Entity\User;
use Reviz\FrontBundle\Entity\Video;
use Reviz\FrontBundle\Entity\Command;

class StudentAccessTest extends BaseTest
{

    /**
     * testCommandsAccessLevelByStudent
     *
     * @test access level lock/unlock student
     */
    public function testCommandsAccessLevelByStudent()
    {
        // data
        //$this->setDataTerm('access_student');
        $add = $this->add();
        // unit test if ok to add new category
        $add('Category', 'addition et multiplication', 'Exercice', 10);
        $add('Category', 'addition et multiplication', 'Method', 15);
        $add('Category', 'addition et multiplication', 'Question', 7);
        $add('Category', 'addition et multiplication', 'Answer', 7);

        // student Tony id 2 and prof Antoine123 id 1
        $this->setUserData('access_student');

        // student
        $student = $this->em
            ->getRepository('RevizFrontBundle:User')
            ->findById(2);

        $category = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findByName('addition et multiplication');

        $this->assertEquals(10, $category[0]->getNbExercice());
        $this->assertEquals(15, $category[0]->getNbMethod());
        $this->assertEquals(7, $category[0]->getNbQuestion());
        $this->assertEquals(7, $category[0]->getNbAnswer());

        // add videos
        $exos = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(3, 'Exercice');

        $this->assertEquals(10, count($exos));

        $generator = $this->generator();

        foreach ($exos as $exo) {
            foreach ($generator(4) as $item) {
                $video = new Video();
                $video->setName('abc' . $item);
                $video->setUrl('abs');
                $exo->addMedia($video);
            }
        }

        // add videos 7 into method
        $methods = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(3, 'Method');

        $this->assertEquals(15, count($methods));

        $generator = $this->generator();

        foreach ($methods as $method) {
            foreach ($generator(7) as $item) {
                $video = new Video();
                $video->setName('abc' . $item);
                $video->setUrl('abs');
                $method->addMedia($video);
            }
        }

        $this->em->flush();

        // check if 4 video by exo into catId 3
        $exos = null;
        $exos = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(3, 'Exercice');

        $this->assertEquals(10, count($exos));

        foreach ($exos as $ex) {

            $videos = $this->em->getRepository('RevizFrontBundle:Post')
                ->getVideos($ex->getId());

            $this->assertEquals(4, count($videos));

        }

        // check if 7 video by method into catId 3
        $methods = null;
        $methods = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(3, 'Method');

        $this->assertEquals(15, count($methods));

        foreach ($methods as $m) {

            $videos = $this->em->getRepository('RevizFrontBundle:Post')
                ->getVideos($m->getId());

            $this->assertEquals(7, count($videos));

        }

        // categories by module module_id=7 user_id = 2
        $categories = null; $category = null;
        $categories = $this->em->getRepository('RevizFrontBundle:Taxonomy')
            ->findById(10);

        $this->assertEquals(count($categories), 1);

        foreach($categories as $category)
        {

            $command = new Command();

            $command->setUser($student[0]);
            $command->setTaxonomy($category);
            $command->setIsLocked(false);

            $posts = $category->getPosts();

            // posts
           foreach ($posts as $post) {

                $serializedPost[] = $post->getId();

               $videos = $this->em->getRepository('RevizFrontBundle:Post')
                   ->getVideos($post->getId());

                foreach($videos as $video)
                {
                    $serializedVideo[] = $video->getId();
                }

            }

            $command->setAccessPosts(json_encode($serializedPost));
            $command->setAccessVideos(json_encode($serializedVideo));

            $this->em->persist($command);
        }

        $this->em->flush();

        // get command by user student student_id = 2
        $myStudentCommand = $this->em->getRepository('RevizFrontBundle:Command')
            ->findByUser(2);

        $this->assertEquals(1, count($myStudentCommand));

        $posts = json_decode($myStudentCommand[0]->getAccessPosts());
        $videos = json_decode($myStudentCommand[0]->getAccessVideos());

        $this->assertEquals((10+15+2*7), count($posts));
        $this->assertEquals((4*10+7*15), count($videos));


    }

}
<?php

namespace Reviz\DataFixtures\ORM;

use Reviz\FrontBundle\Entity\Command;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCommandData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $module = $manager
            ->getRepository('Reviz\FrontBundle\Entity\Module')
            ->findByName('suite');

        $categories = $manager
            ->getRepository('Reviz\FrontBundle\Entity\Taxonomy')
            ->allCategoriesByModule($module[0]);

        $user = $manager
            ->getRepository('Reviz\FrontBundle\Entity\User')
            ->findByEmail('antoine@gmail.com');

        $serializedPost = [];
        $serializedVideo = [];

        foreach ($categories as $category) {
            $command = new Command();
            $command->setUser($user[0]);
            $command->setTaxonomy($category);
            $command->setIsLocked(false);

            $posts = $category->getPosts();

            if(count($posts) == 0) continue;

            foreach ($posts as $post) {

                $serializedPost[] = $post->getId();

                $videos = $manager->getRepository('RevizFrontBundle:Post')
                    ->getVideos($post->getId());

                foreach ($videos as $video) {
                    $serializedVideo[] = $video->getId();
                }

            }

            $command->setAccessPosts(json_encode($serializedPost));
            $command->setAccessVideos(json_encode($serializedVideo));

            $manager->persist($command);
            $serializedVideo = [];
            $serializedPost = [];

        }

        $manager->flush();

    }

    public function getOrder()
    {
        return 5;
    }

}
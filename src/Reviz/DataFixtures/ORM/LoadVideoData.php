<?php

namespace Reviz\DataFixtures\ORM;

use Reviz\FrontBundle\Entity\Video;
use Reviz\FrontBundle\Entity\Command;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadVideoData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $module = $manager
            ->getRepository('Reviz\FrontBundle\Entity\Taxonomy')
            ->findByName('suite');

        $categories = $manager
            ->getRepository('Reviz\FrontBundle\Entity\Taxonomy')
            ->allCategoriesByModule($module[0]);

        foreach ($categories as $category) {
            $posts = $category->getPosts();
            foreach ($posts as $post) {
                foreach (range(1, 5) as $item) {
                    $video = new Video();
                    $video->setName('abc' . $item);
                    $video->setUrl('abs');
                    $post->addMedia($video);
                    $manager->persist($video);
                }
                $manager->persist($post);
            }
        }
        $manager->flush();

    }

    public function getOrder()
    {
        return 4;
    }

}
<?php

namespace Reviz\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Apoutchika\LoremIpsumBundle\Services\LoremIpsum;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Reviz\FrontBundle\Entity\Answer as RevizAnswer;
use Reviz\FrontBundle\Entity\User;

class LoadCorrectionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $lorem = new LoremIpsum(2, 8, 5, 10,
            10, 10, 3, NULL);

        $professor = $manager
            ->getRepository('RevizFrontBundle:User')
            ->findByUsername('AntoineL');

        $add = function ($title) use ($manager, $lorem, $professor) {
            $category = $manager
                ->getRepository('RevizFrontBundle:Taxonomy')
                ->findByName($title);

            $posts = $category[0]->getPosts();

            $tags = $manager
                ->getRepository('RevizFrontBundle:Tag')
                ->findAll();
            $max = count($tags);
            foreach($posts as $post)
             {

                 $answer = new RevizAnswer();
                 $answer->setTitle('correction: '.$post->getTitle());
                 $answer->setContent('blabla');
                 $answer->setPostParent((int) $post->getId());
                 $answer->addTaxonomy($category[0]);
                 $answer->addTaxonomy($tags[rand(0, $max-1)]);
                 $answer->setUser($professor[0]);

                 $manager->persist($answer);
             }

        };

        $add('limite suite');
        $add('raisonnement par récurrence');

        $manager->flush();

    }

    public function getOrder()
    {
        return 6;
    }

}
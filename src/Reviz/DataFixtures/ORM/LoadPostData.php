<?php

namespace Reviz\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Apoutchika\LoremIpsumBundle\Services\LoremIpsum;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Reviz\FrontBundle\Entity\User;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $lorem = new LoremIpsum(2, 8, 5, 10,
            10, 10, 3, NULL);

        $professor = $manager
            ->getRepository('RevizFrontBundle:User')
            ->findByUsername('AntoineL');

        $add = function ($title, array $data, $customName) use ($manager, $lorem, $professor) {
            $customName = ucfirst(strtolower($customName));
            $entity = 'Reviz\FrontBundle\Entity\\' . $customName;

            $category = $manager
                ->getRepository('RevizFrontBundle:Taxonomy')
                ->findByName($title);

            $tags = $manager
                ->getRepository('RevizFrontBundle:Tag')
                ->findAll();
            $max = count($tags);

            foreach ($data as $name) {
                $post = new $entity;
                $post->setTitle($name);
                $post->setContent($lorem->getParagraphs(rand(2, 5)));
                $post->addTaxonomy($category[0]);
                $post->addTaxonomy($tags[rand(0, $max-1)]);
                $post->setUser($professor[0]);

                $manager->persist($post);
            }

        };

        // 6eme categories memory

        $exercices = [
            'reconnaitre une situation proportionnelle',
            'utilisation de la linéarité',
            'appliquer un taux de pourcentage',
        ];
        $add('propriété de linéarité', $exercices, 'exercice');

        $methods = [
            'calculer un pourcentage'
        ];
        $add('propriété de linéarité', $methods, 'method');

        $cours = [
            'la proportionnalité',
        ];

        $add('propriété de linéarité', $cours, 'course');

        // terminale S option maths

        $exercices = [
            'Démonter par récurrence somme des N premiers entiers',
            'somme des carrés',
            'somme des cubes',
        ];

        $add('raisonnement par récurrence', $exercices, 'exercice');

        $manager->flush();

    }

    public function getOrder()
    {
        return 3;
    }

}
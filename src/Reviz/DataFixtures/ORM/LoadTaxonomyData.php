<?php

namespace Reviz\DataFixtures\ORM;

use Reviz\FrontBundle\Entity\Tag;
use Reviz\FrontBundle\Entity\Level;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class LoadTaxonomyData  extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $levels = [
            "6 eme",
            "5 eme",
            "Première S",
            "Terminale S option maths",
        ];

        foreach($levels as $name)
        {
            $taxonomy = new Level();
            $taxonomy->setName($name);

            $manager->persist($taxonomy);
        }

        // first flush because after we need level ...
        $manager->flush();
        $manager->clear();

        // add module by level
        $add = function($title, array $data, $parentName) use ($manager)
        {
            $parentName = ucfirst(strtolower($parentName));
            $entity = 'Reviz\FrontBundle\Entity\Module';

            if($parentName == 'Module') $entity = 'Reviz\FrontBundle\Entity\Category';

            $repository = 'RevizFrontBundle:'.$parentName;

            $level = $manager
                ->getRepository($repository)
                ->findByName($title);

            foreach($data as $name)
            {
                $taxonomy = new $entity();
                $taxonomy->setName($name);
                $taxonomy->setParentId($level[0]->getId());

                $manager->persist($taxonomy);
            }

            $manager->flush();
            $manager->clear();
        };

        // add module by level
        $modules =[
            'proportionnalité',
        ];
        $add("6 eme", $modules, 'level');

        // add categories by into proportionnalité
        $categories = [
            'propriété de linéarité',
            'tableau de proportionnalité',
            'pourcentage'
        ];
        // title of module
        $add("proportionnalité", $categories, 'module');

        // 5ème
        $modules =[
            'Proportionnalité',
        ];
        $add("5 eme", $modules, 'level');

        $categories = [
            'propriété de linéarité en cinquième',
            'tableau de proportionnalité',
            'pourcentage'
        ];
        $add("Proportionnalité", $categories, 'module');

        // première S
        $modules =[
            'second degré',
            'étude de fonction',
            'dérivation',
        ];
        $add("Première S", $modules, 'level');

        $categories = [
            'forme canonique',
            'fonction polymone',
            'équation du second degré',
            'signe du trinome',
        ];
        $add("second degré", $categories, 'module');

        $categories = [
            'fonction de référence',
        ];
        $add("étude de fonction", $categories, 'module');

        $categories = [
            'nombre dérivé',
            'tangente',
            'dérivée somme'
        ];
        $add("dérivation", $categories, 'module');

        // terminale S
        $modules =[
            'suite',
            'opération sur les limites',
            'limites fonctions',
        ];
        $add('Terminale S option maths', $modules, 'level');

        $categories = [
            'raisonnement par récurrence',
            'limite suite',
            'comparaison'
        ];
        $add("suite", $categories, 'module');

        $categories = [
            'comportement à l\'infini',
            'suite majoré'
        ];
        $add("opération sur les limites", $categories, 'module');

        $categories = [
            'limites',
        ];
        $add("limites fonctions", $categories,'module');

        // tags
        $tags=[
            'maths',
            'nombre premier',
            'carré parfait',
            'nombre ami'
        ];

        foreach ($tags as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
        }

        $manager->flush();

    }

    public function getOrder()
    {
        return 2;
    }
}
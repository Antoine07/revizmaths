<?php

namespace Tests\FrontBundle;

use Reviz\FrontBundle\Entity\Exercice;
use Reviz\FrontBundle\Entity\User;

/**
 * all tests based to a specifics data in BaseTest
 *
 * Class TaxonomyTest
 * @package Test\FrontBundle
 */
class TaxonomyTest extends BaseTest
{

    public function testCreateTermsTaxonomyLevelModuleCategory()
    {

        $levels = $this->em
            ->getRepository('RevizFrontBundle:Level')
            ->findAll();

        $this->assertCount(6, $levels);

        $modules = $this->em
            ->getRepository('RevizFrontBundle:Module')
            ->findAll();

        $this->assertCount(3, $modules);

        $cat = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findAll();

        $this->assertCount(2, $cat);

    }

    public function testNumberModuleByLevel()
    {
        $modules = $this->em
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->allModulesByLevel("6 eme");

        $this->assertCount(2, $modules);

    }

    public function testNbCustomPostTypeByCategoryAndModule()
    {

        $add = $this->add();

        $add('Category', 'addition et multiplication', 'Exercice', 78);
        $add('Category', 'les tables de multiplication', 'Exercice', 12);

        $add('Category', 'addition et multiplication', 'Method', 12);
        $add('Category', 'les tables de multiplication', 'Method', 13);

        $add('Category', 'addition et multiplication', 'Answer', 45);
        $add('Category', 'les tables de multiplication', 'Answer', 33);

        // todo nb exo, method, answer by level

        // nb exo by category
        $nbResourceCat = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findByName('addition et multiplication');

        $this->assertEquals(78, $nbResourceCat[0]->getNbExercice());

        // nb method by category
        $this->assertEquals(12, $nbResourceCat[0]->getNbMethod());

        // nb answer by category
        $this->assertEquals(45, $nbResourceCat[0]->getNbAnswer());

        // nb exo by category
        $nbResourceCat = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findByName('les tables de multiplication');

        $this->assertEquals(12, $nbResourceCat[0]->getNbExercice());

        // nb method by category
        $this->assertEquals(13, $nbResourceCat[0]->getNbMethod());

        // nb answer by category
        $this->assertEquals(33, $nbResourceCat[0]->getNbAnswer());

        // nb rousource by module
        $nbMethodArithm = $this->em
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->nbResourceByModule('Method', 'arithmétique');

        $this->assertEquals((12 + 13), $nbMethodArithm[0]['nb']);

        $nbAnswerArithm = $this->em
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->nbResourceByModule('Answer', 'arithmétique');

        $this->assertEquals((45 + 33), $nbAnswerArithm[0]['nb']);

        $nbExoArithm = $this->em
            ->getRepository('RevizFrontBundle:Taxonomy')
            ->nbResourceByModule('Exercice', 'arithmétique');

        $this->assertEquals((78 + 12), $nbExoArithm[0]['nb']);

        // remove resource exo into category id 10 addition et multiplication
        $limit = 5;
        $exos = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(10, 'Exercice', $limit);

        foreach ($exos as $exo) $this->em->remove($exo);

        // nb exo by category
        $nbResourceCat = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findByName('addition et multiplication');

        $this->assertEquals((78 - 5), $nbResourceCat[0]->getNbExercice());

        $limit = 9;
        $methods = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(10, 'Method', $limit);

        foreach ($methods as $method) $this->em->remove($method);

        // nb exo by category
        $nbResourceCat = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findByName('addition et multiplication');

        $this->assertEquals((12 - 9), $nbResourceCat[0]->getNbMethod());

        $limit = 44;
        $answers = $this->em->getRepository('RevizFrontBundle:Post')
            ->allPostByTermId(10, 'Answer', $limit);

        foreach ($answers as $answer) $this->em->remove($answer);

        // nb exo by category
        $nbResourceCat = $this->em
            ->getRepository('RevizFrontBundle:Category')
            ->findByName('addition et multiplication');

        $this->assertEquals((45 - 44), $nbResourceCat[0]->getNbAnswer());

    }

    public function testCategoriesByPost()
    {
        $add = $this->add();

        $add('Category', 'addition et multiplication', 'Exercice', 2);

        $exercices = $this->em
            ->getRepository('RevizFrontBundle:Exercice')
            ->findAll();

        foreach($exercices as $exercice)
        {
            $category = $this->em
                ->getRepository('RevizFrontBundle:Taxonomy')
                ->getCategory($exercice->getId());

            $this->assertEquals('addition et multiplication', $category->getName());
        }

    }

}
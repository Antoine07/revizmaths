<?php

namespace Reviz\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Apoutchika\LoremIpsumBundle\Services\LoremIpsum;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Reviz\FrontBundle\Entity\User;


class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $professor = new User;
        $professor->setUsername('AntoineL');
        $professor->setEmail('antoine.lucsko@gmail.com');
        $professor->setPassword('Antoine' );
        $professor->setEnabled(true);
        $professor->addRole('ROLE_PROFESSOR');

        $manager->persist($professor);

        // students
        $student = new User;
        $student->setUsername('AntoineM');
        $student->setEmail('antoine@gmail.com');
        $student->setPassword('AntoineM' );
        $student->addRole('ROLE_STUDENT');

        $manager->persist($student);

        $student2 = new User;
        $student2->setUsername('Simon');
        $student2->setEmail('simon@gmail.com');
        $student2->setPassword('Simon' );
        $student2->addRole('ROLE_STUDENT');

        $manager->persist($student2);

        $manager->flush();

    }

    public function getOrder()
    {
        return 1;
    }

}
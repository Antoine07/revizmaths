<?php

namespace Reviz\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container=null )
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $userManager = $this->container->get('fos_user.user_manager');

        $userAdmin = $userManager->createUser();

        $userAdmin->setUsername('AntoineL');
        $userAdmin->setEmail('antoine@example.com');
        $userAdmin->setPlainPassword('Antoine');
        $userAdmin->setEnabled(true);
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $userManager->updateUser($userAdmin, true);

        // students
        $student = $userManager->createUser();

        $student->setUsername('AntoineM');
        $student->setEmail('antoinem@example.com');
        $student->setPlainPassword('AntoineM');
        $student->setEnabled(true);
        $student->setRoles(['ROLE_STUDENT']);

        $userManager->updateUser($student, true);

        $student2 = $userManager->createUser();

        $student2->setUsername('Simon');
        $student2->setEmail('simon@example.com');
        $student2->setPlainPassword('Simon');
        $student2->setEnabled(true);
        $student2->setRoles(['ROLE_STUDENT']);

        $userManager->updateUser($student2, true);

    }

    public function getOrder()
    {
        return 1;
    }

}
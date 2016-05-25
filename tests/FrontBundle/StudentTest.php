<?php

namespace Test\FrontBundle;

use Reviz\FrontBundle\Entity\User;

class StudentTest extends BaseTest
{

    /**
     * testProfHaveManyStudentAndInverse
     *
     * @test student have many profs and prof have many students
     */
    public function testProfHaveManyStudentAndInverse()
    {
        /*$add = $this->add();
        $add('Category', 'addition et multiplication', 'Exercice', 20);*/

        // one user have many profs

        $student = new User;
        $student->setUsername('Tony');
        $student->setEmail('tony.lucsko@gmail.com');
        $student->addRole('ROLE_STUDENT');
        $student->setPassword('Tony');

        $generator = $this->generator();

        // profs
        foreach ($generator(4) as $item) {
            $prof = new User;
            $prof->setUsername('Antoine' . $item);
            $prof->setEmail('antoine.lucsko@gmail.com' . $item);
            $prof->setPassword('Antoine' . $item);
            $prof->addRole('ROLE_PROFESSOR');

            $student->addMyProf($prof);

            $this->em->persist($prof);

        }

        $this->em->persist($student);

        $this->em->flush();

        $profs = $this->em
            ->getRepository('RevizFrontBundle:User')
            ->getRole('ROLE_PROFESSOR');

        $this->assertCount(4, $profs);

        $student = $this->em
            ->getRepository('RevizFrontBundle:User')
            ->findByUsername('Tony');

        $this->assertCount(1, $student);

        $item = 0;
        foreach ($student[0]->getMyProfs() as $prof) {
            $myProfs[] = $prof->getUsername();
            $item++;
            $this->assertContains('Antoine' . $item, $myProfs);
        }

        // one prof have many students
        $profs = null;
        $prof = null;
        $prof = new User;
        $prof->setUsername('Simon');
        $prof->setEmail('simon.lucsko@gmail.com');
        $prof->addRole('ROLE_PROFESSOR');
        $prof->setPassword('Simon');

        // generate students
        foreach ($generator(15) as $item) {
            $student = new User;
            $student->setUsername('AntoineM' . $item);
            $student->setEmail('antoinem.lucsko@gmail.com' . $item);
            $student->setPassword('AntoineM' . $item);
            $student->addRole('ROLE_STUDENT');
            $prof->addMyStudent($student);

            $this->em->persist($student);
        }

        $this->em->persist($prof);
        $this->em->flush();

        $prof = $this->em
            ->getRepository('RevizFrontBundle:User')
            ->findByUsername('Simon');

        $this->assertCount(1, $prof);

        $nbStudent = 0;
        foreach ($prof[0]->getMyStudents() as $student) {
            $myStudents[] = $student->getUsername();
            $nbStudent++;
            $this->assertContains('AntoineM' . $nbStudent, $myStudents);
        }

        $this->assertEquals($nbStudent, 15);

    }
}
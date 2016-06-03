<?php

namespace Test\FrontBundle;

use Reviz\FrontBundle\Twig\FrontExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TwigExtensionTest extends KernelTestCase
{

    /**
     * @dataProvider humanRoleProvider
     */
    public function testFilterHumanRole($roleName, $expect)
    {
        $filter = new FrontExtension();

        $output = $filter->humanRoleFilter($roleName);

        $this->assertEquals($output, $expect);
    }

    public static function humanRoleProvider()
    {
        return [
            ['ROLE_ADMIN', "administrator"],
            ['ROLE_PROFESSOR', "professor"],
            ['ROLE_STUDENT', "student"],
            ['ROLE_USER', "user"],
            ['ROLE_NOTHING', "anonymous"],
        ];
    }

}
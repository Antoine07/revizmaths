<?php

namespace Tests\FrontBundle;

use Reviz\FrontBundle\Entity\User;
use Reviz\FrontBundle\Entity\Video;
use Reviz\FrontBundle\Entity\Command;

class RegexTest extends BaseTest
{
    /**
     * @dataProvider regexTextPassword
     */
    public function testPassword($password, $excepted)
    {
        // start letter and letter number underscore min and content 2 numbers min
        $testPassword = (boolean) preg_match('/[a-zA-Z]\w(?=(.*[0-9]){2,}){5,}/', $password);

        $this->assertEquals($testPassword, $excepted);
    }

    public static function regexTextPassword()
    {
        return [
            ['aannBB44', true ],
            ['44ajkjkj', false ],
            ['qjhdsjAbG45fjshjdhfABB', true ],
            ['454548', false ],
            ['a', false ],
            ['4hjhj', false ],
            ['jkjkj4', false ],
            ['aaaaa', false ],
            ['AB04583jj', true ],
            ['A_B04583jj', true ],
            ['A45', false ],
            ['Ab45c', true ],
            ['Abc45', true ],
            ['A45bc4', true ],
            ['A45b', false ],
        ];
    }

    /**
     * @dataProvider regexNumberPhone
     */
    public function testNumberPhone($number, $excepted)
    {
        $testNumber = (boolean) preg_match('/^\+?([0-9] ?){8}[0-9]{2}$/', $number);

        $this->assertEquals($testNumber, $excepted);
    }

    public static function regexNumberPhone()
    {
        return [
            ['45 45 45 45 45', true ],
            ['4545454545', true ],
            ['45454545', false ],
            ['45 4545 4545', true ],
            ['+334545 4545', true ],
        ];
    }

}
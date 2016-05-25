<?php

namespace Reviz\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RevizUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

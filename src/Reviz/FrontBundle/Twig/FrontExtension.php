<?php

namespace Reviz\FrontBundle\Twig;

class FrontExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('human_role', [$this, 'humanRoleFilter']),
            new \Twig_SimpleFilter('wrapper_role', [$this, 'wrapperRoleFilter']),
        );
    }

    public function humanRoleFilter($roleName)
    {
        $hierarchyRoles = [
            'ROLE_ADMIN' => 'administrator',
            'ROLE_PROFESSOR' => 'professor',
            'ROLE_STUDENT' => 'student',
            'ROLE_USER' => 'user'
        ];

        if(isset($hierarchyRoles[$roleName])) return $hierarchyRoles[$roleName];

        return 'anonymous';
    }

    public function wrapperRoleFilter($professorName)
    {

    }

    public function getName()
    {
        return 'reviz_extension';
    }
}

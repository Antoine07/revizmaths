<?php

namespace Reviz\FrontBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{

    public function getRole($roleName)
    {

        $query = $this->getEntityManager()
            ->createQuery("
                SELECT u
                FROM RevizFrontBundle:User u
                WHERE u.roles LIKE :roleName
            ");

        $query->setParameter('roleName', '%'.$roleName.'%');

        var_dump($query->getSql());

        return $query->getResult();

    }

}

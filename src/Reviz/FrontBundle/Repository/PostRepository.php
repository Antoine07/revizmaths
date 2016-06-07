<?php

namespace Reviz\FrontBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * allPostByTermId
     *
     * @param $termId
     * @return array
     */
    public function allPostByTermId($termId, $customType = 'Post', $limit = -1)
    {
        $customType = 'RevizFrontBundle:' . ucfirst(strtolower($customType));

        $dql = sprintf('
                 SELECT p
                 FROM %s p
                 JOIN p.taxonomies t
                 WHERE t.id = :termId
                 ',
            $customType
        );

        $query = $this->getEntityManager()
            ->createQuery($dql);

        $query->setParameter('termId', $termId);

        if ($limit != -1 && $limit > 0)
            $query->setMaxResults($limit);

        return $query->getResult();
    }

    public function getVideos($postId)
    {

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Reviz\FrontBundle\Entity\Video', 'v');
        $rsm->addFieldResult('v', 'id', 'id');
        $rsm->addFieldResult('v', 'url', 'url');
        $rsm->addFieldResult('v', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery('
                      SELECT v.id, v.url, v.name
                      FROM medias as v
                      INNER JOIN post_media as pm
                      ON pm.media_id = v.id
                      WHERE pm.post_id = ?', $rsm
            );

        //var_dump($query->getSql());

        $query->setParameter(1, $postId);


        return $query->getResult();
    }

    public function getImages($postId)
    {

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Reviz\FrontBundle\Entity\Image', 'im');
        $rsm->addFieldResult('im', 'id', 'id');
        $rsm->addFieldResult('im', 'url', 'url');
        $rsm->addFieldResult('im', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery('
                      SELECT im.id, im.url, im.name
                      FROM medias as im
                      INNER JOIN post_media as pm
                      ON pm.media_id = im.id
                      WHERE pm.post_id = ?', $rsm
            );

        $query->setParameter(1, $postId);

        return $query->getResult();
    }

    public function getLevel()
    {

    }

}

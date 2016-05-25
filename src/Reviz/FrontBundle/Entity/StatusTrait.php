<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 12/05/2016
 * Time: 15:49
 */

namespace Reviz\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait StatusTrait
{

    /**
     * @ORM\Column(name="status", type="string", length=20, nullable=false)
     */
    private $status;

    /**
     * set status
     *
     * @param  string $status
     */
    public function setStatus($status)
    {
        if (!in_array($status, [
            'published',
            'unpublished',
            'draft',
            'deleted',
            'finished'
        ])
        )
            throw new \InvalidArgumentException("Invalid status");

        $this->status = $status;

        return $this;
    }


    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }


}
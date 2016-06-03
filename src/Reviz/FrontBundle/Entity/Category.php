<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 11/05/2016
 * Time: 22:52
 */

namespace Reviz\FrontBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table(name="taxonomies")
 */
class Category extends Taxonomy
{
}

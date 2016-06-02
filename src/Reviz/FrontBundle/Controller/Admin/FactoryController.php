<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 02/06/2016
 * Time: 07:16
 */

namespace Reviz\FrontBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class FactoryController
{



    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function indexAction($name)
    {
        return $this->templating->renderResponse(
            'AppBundle:Hello:index.html.twig',
            array('name' => $name)
        );
    }

}
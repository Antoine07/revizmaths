<?php

namespace Reviz\FrontBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TrumbowygType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => ['class' => ''] // On ajoute la classe
        ));
    }

    public function getParent() // On utilise l'héritage de formulaire
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'trumbowyg';
    }
}
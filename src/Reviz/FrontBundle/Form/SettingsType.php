<?php
/**
 * Created by PhpStorm.
 * User: simonarruti
 */

namespace Reviz\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('label' => 'Nom'))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'first_options' => array(
                    'label' => 'Mot de passe'
                ),
                'second_options' => array(
                    'label' => 'Confirmer le mot de passe'
                )
            ))
            ->add('filter_modules', CheckboxType::class, array(
                'label' => 'Lié à ses modules',
                'required' => false
            ))
            ->add('filter_info', CheckboxType::class, array(
                'label' => 'Informatique',
                'required' => false
            ))
            ->add('filter_all', CheckboxType::class, array(
                'label' => 'Tout',
                'required' => false
            ))
            ->add('submit', SubmitType::class, array('label' => 'Valider'))
            ->getForm()
        ;
    }

}
<?php

namespace Reviz\FrontBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();
        $userId = $user->getId();

        $labels = [
            'administrator' => 'ROLE_ADMIN',
            'professor' => 'ROLE_PROFESSOR',
            'student' => 'ROLE_STUDENT',
            'user' => 'ROLE_USER'
        ];

        $selected = [];

        if (isset($options['requiredPasswor'])) {
            $options['first_password'] = 'reset your password';
            $options['second_password'] = 'if reset you must repeat then';
        }

        foreach ($user->getRoles() as $roleName) {
            if (!in_array($roleName, $labels)) continue;

            $selected[] = $labels;
        }

        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Username'
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email'
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'label' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => $options['requiredPassword'],
                'first_options' => ['label' => $options['first_password']],
                'second_options' => ['label' => $options['second_password']],
                'invalid_message' => 'your password do not match with politic security',
            ))
            ->add('address')
            ->add('phone')
            ->add('myProfs', EntityType::class, [
                'class' => 'Reviz\FrontBundle\Entity\User',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->getProfs();
                },
                'label' => 'Choice professor',
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'block_name' => 'profs',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => $labels,
                'multiple' => true,
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reviz\FrontBundle\Entity\User',
            'requiredPassword' => true,
            'first_password' => 'give your password',
            'second_password' => 'repeat then',
        ));
    }
}

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

        foreach ($user->getRoles() as $roleName) {
            if (!in_array($roleName, $labels)) continue;

            $selected[] = $labels;
        }

        $builder
            ->add('username', TextType::class, [
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'required' => true
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => $options['requiredPassword'],
                'first_options' => ['label' => 'reset your password'],
                'second_options' => ['label' => 'if reset you must repeat then'],
            ))
            ->add('address')
            ->add('phone')
            ->add('myProfs', EntityType::class, [
                'class' => 'Reviz\FrontBundle\Entity\User',
                'query_builder' => function (EntityRepository $repo) use ($userId) {
                    return $repo->getProfs($userId);
                },
                'label' => 'choisir mes profs: ',
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
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
            'requiredPassword' => true
        ));
    }
}

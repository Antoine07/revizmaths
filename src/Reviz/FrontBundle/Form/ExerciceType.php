<?php
namespace Reviz\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ExerciceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['data' => 'caca','required' => true])
            ->add('content', TextareaType::class, ['data' => 'caca','required' => false])
            ->add('status', ChoiceType::class, ['choices' => [
                'publié' => 'published', 'dépublié' => 'unpublished'
            ]])
            ->add('createdAt', DateTimeType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-default pull-right')
            ])
            ->add('delete', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reviz\FrontBundle\Entity\Post'
        ));

    }
}
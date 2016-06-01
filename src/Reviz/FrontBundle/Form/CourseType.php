<?php

namespace Reviz\FrontBundle\Form;

use Doctrine\ORM\EntityRepository;
use Reviz\FrontBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'Publié' => 'published',
                    'Non publié' => 'unpublished'
                )
            ))
            /*->add('taxonomies', EntityType::class, array(
                'label' => 'Modules',
                'class' => 'Reviz\\FrontBundle\\Entity\\Post',
                'query_builder' => function (EntityRepository $repo) {
                    $qb = $repo->createQueryBuilder('m');
                    $qb->where('m.term=:term');
                    $qb->setParameter('term', 'module');
                    return $qb;
                }
            ))*/

            ->add('course', EntityType::class, array(
                    'class' => 'RevizFrontBundle:Module',
                    'choice_label' => 'name',
                    'multiple' => false,
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reviz\FrontBundle\Entity\Course'
        ));
    }
}

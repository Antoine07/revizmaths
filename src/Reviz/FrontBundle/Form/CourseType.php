<?php

namespace Reviz\FrontBundle\Form;

use Doctrine\ORM\EntityRepository;
use Reviz\FrontBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
            ->add('taxonomies', EntityType::class, array(
                'label' => 'Taxonomies',
                'class' => 'Reviz\\FrontBundle\\Entity\\Taxonomy',
                'query_builder' => function (EntityRepository $repo) {
                    $qb = $repo->createQueryBuilder('t');
                    //$qb = $repo->findAll();
                    //dump($qb); die;
                    return $qb;
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))/*   ->add('taxonomies',  EntityType::class, array(
                'class' => 'Reviz\FrontBundle\Entity\Level',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('taxonomies',  EntityType::class, array(
                'class' => 'Reviz\FrontBundle\Entity\Module',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))*/
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

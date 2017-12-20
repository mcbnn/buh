<?php

namespace AppBundle\Form;

use AppBundle\Entity\BuhDirection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\BuhSchet;
use AppBundle\Repository\BuhDirectionRepository;

class BuhSchetForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('schet', TextType::class, ['label' => 'Счет'])
            ->add('id_direction', EntityType::class,
                [
                    'class' => BuhDirection::class,
                    'query_builder' => function (BuhDirectionRepository $er) {
                        return $er->getBuhDirection();
                    },
                    'choice_label' => 'shortName',
                    'required' => true
                ])
            ->add('save', SubmitType::class,
                [
                    'attr'  => ['class' => 'btn btn-success'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuhSchet::class
        ]);
    }
}
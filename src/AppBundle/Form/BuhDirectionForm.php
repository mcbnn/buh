<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\BuhDirection;


class BuhDirectionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias', TextType::class, ['label' => 'alias'])
            ->add('full_name', TextType::class, ['label' => 'Полное название'])
            ->add('short_name', TextType::class, ['label' => 'Сокращеное название'])
            ->add('firebird', TextType::class, ['label' => 'firebird'])
            ->add('save', SubmitType::class,
                [
                    'attr'  => ['class' => 'btn btn-success'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuhDirection::class
        ]);
    }
}
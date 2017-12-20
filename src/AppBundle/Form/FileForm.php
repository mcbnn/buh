<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\BuhHistory;


class FileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachment', FileType::class,
                [
                    'label' => 'Файл для загрузки',
                    'required' => true,
                    'data_class' => null
                ]
            )
            ->add('save', SubmitType::class,
                [
                    'attr'  => ['class' => 'btn btn-success'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuhHistory::class
        ]);
    }
}
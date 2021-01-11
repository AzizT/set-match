<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('username', TextType::class)
            // ->add('category', TextType::class)
            ->add('searchDateEngine', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
            ])
            // ->add('prenom', TextType::class)
            // ->add('presentation', TextType::class)
            // ->add('besoins', TextType::class)
            // ->add('langues', TextType::class)
            // ->add('specialites', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

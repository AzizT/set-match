<?php

namespace App\Form;

use App\Entity\Langue;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use  App\Form\PlanningType;

class UpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)

            ->add('nom', TextType::class)

            ->add('prenom', TextType::class)

            ->add('email', EmailType::class)
            
            ->add('presentation', TextType::class)

            ->add('besoins', TextType::class)

            ->add('category', TextType::class)

            ->add('Password', PasswordType::class, [
                'required' => true
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Joueur/Player' => 'player',
                    'Coach' => 'coach',
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
            ])
            ->add('plannings', CollectionType::class, [
                'entry_type' => PlanningType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true
            ])
            ->add('langues', EntityType::class, [
                'class' => Langue::class,
                'choices' => $options['langues'],
                'expanded' => true,
                'multiple' => true
            ])
            ->add('specialites', EntityType::class, [
                'class' => Specialite::class,
                'choices' => $options['specialites'],
                'expanded' => true,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'langues' => [],
            'specialites' => []
        ]);
    }
}

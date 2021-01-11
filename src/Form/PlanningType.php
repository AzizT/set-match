<?php

namespace App\Form;

use App\Entity\Planning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Lieu;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateType::class,[
                'attr' => ['id' => 'dateDebut'],
                'widget' => 'single_text',
                // 'constraints' => [
                //     new Constraints\NotBlank(),
                //     new Constraints\DateTime(),
                // ]
            ])
            ->add('dateFin', DateType::class, [
                'attr' => ['id' => 'dateFin'],
                'widget' => 'single_text',
                // 'constraints' => [
                //     new Constraints\NotBlank(),
                //     new Constraints\DateTime(),
                //     new Constraints\Callback(function($object, ExecutionContextInterface $context) {
                //         $dateDebut = $context->getRoot()->getData()['dateDebut'];
                //         $dateFin = $object;
        
                //         if (is_a($dateDebut, \DateTime::class) && is_a($dateFin, \DateTime::class)) {
                //             if ($dateFin->format('U') - $dateDebut->format('U') < 0) {
                //                 $context
                //                     ->buildViolation('La date de fin doit etre supérieur a la date de début')
                //                     ->addViolation();
                //             }
                //         }
                //     }),
                // ],
            ])
            ->add('lieu', EntityType::class, [
                // looks for choices from this entity
                'class' => Lieu::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'city',
            
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}

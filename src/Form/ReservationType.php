<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Heures disponibles (Midi/Soir)
        $heureChoices = [
            'Midi' => [
                '12:00' => '12:00',
                '12:15' => '12:15',
                '12:30' => '12:30',
                '12:45' => '12:45',
                '13:00' => '13:00',
                '13:15' => '13:15',
                '13:30' => '13:30',
                '13:45' => '13:45',
            ],
            'Soir' => [
                '19:00' => '19:00',
                '19:15' => '19:15',
                '19:30' => '19:30',
                '19:45' => '19:45',
                '20:00' => '20:00',
                '20:15' => '20:15',
                '20:30' => '20:30',
                '20:45' => '20:45',
                '21:00' => '21:00',
                '21:15' => '21:15',
                '21:30' => '21:30',
                '21:45' => '21:45',
            ]
        ];

        $builder
            ->add('nbCouvert', IntegerType::class, [
                'label' => 'Nombre de couverts',
                'attr' => [
                    'min' => 1,
                    'max' => 15,
                    'id' => 'nbCouvert',
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 15,
                        'notInRangeMessage' => 'Vous devez réserver entre {{ min }} et {{ max }} couverts.',
                    ]),
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text', // affiche un champ HTML5 avec calendrier
                'html5' => true,           // pour activer le picker natif du navigateur
                'label' => 'Date de réservation',
                'required' => true,
            ])
            ->add('heure', ChoiceType::class, [
                'choices' => $heureChoices, // ton tableau Midi/Soir
                'expanded' => true,         // boutons radio
                'multiple' => false,        // un seul choix possible
                'label' => 'Heure du repas',
            ])
            ->add('allergiesActive', ChoiceType::class, [
                'label' => 'Allergies',
                'choices' => ['Oui' => true, 'Non' => false],
                'expanded' => true,
                'multiple' => false,
                'data' => false,
            ])
            ->add('allergies', TextareaType::class, [
                'label' => 'Préciser vos allergies',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select date',
                ],
                'required' => true,
            ])
            ->add('celsius', NumberType::class, [
                'attr' => [
                    'min' => -100,
                    'max' => 100,
                    'class' => 'form-control',
                    'placeholder' => 'Enter temperature in Celsius',
                ],
                'required' => true,
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'city', // Możesz zmienić na inne pole np. "city" lub "id"
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}

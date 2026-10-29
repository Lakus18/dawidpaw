<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => static fn (Location $location): string => $location->getCity().' ('.$location->getCountry().')',
                'placeholder' => 'Wybierz lokalizację',
                'label' => 'Lokalizacja',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'label' => 'Data prognozy',
            ])
            ->add('celsius', NumberType::class, [
                'input' => 'string',
                'scale' => 0,
                'html5' => true,
                'label' => 'Temperatura (°C)',
                'attr' => ['min' => -100, 'max' => 100, 'step' => 1],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Measurement::class]);
    }
}

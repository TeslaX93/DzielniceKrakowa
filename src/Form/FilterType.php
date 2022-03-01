<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [
          'Nazwa' => 'name',
          'Powierzchnia' => 'area',
          'Miasto' => 'city',
          'Populacja' => 'population'
        ];

        $builder
            ->add('column', ChoiceType::class, ['label' => 'Kolumna','choices' => $choices])
            ->add('value', TextType::class, ['label' => 'Fraza'])
            ->add('submit', SubmitType::class, ['label' => 'Filtruj'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

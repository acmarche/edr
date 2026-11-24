<?php

namespace AcMarche\Edr\Sante\Form;

use AcMarche\Edr\Entity\Sante\SanteQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SanteReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $choices = array_flip(['Non', 'Oui']);
        $formBuilder
            ->add(
                'reponseTxt',
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'placeholder' => false,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'remarque',
                TextType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => SanteQuestion::class,
            ]
        );
    }
}

<?php

namespace AcMarche\Edr\Relation\Form;

use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Relation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'type',
                TextType::class,
                [
                    'label' => '',
                    'help' => 'Papa, maman, oncle, belle-maman...)',
                    'required' => false,
                ]
            )
            ->add(
                'ordre',
                ChoiceType::class,
                [
                    'choices' => EdrConstantes::ORDRES,
                    'help' => 'Permet de forcer l\'ordre si celui est différent (Famille recomposée)',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Relation::class,
            ]
        );
    }
}

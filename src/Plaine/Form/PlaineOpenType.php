<?php

namespace AcMarche\Edr\Plaine\Form;

use AcMarche\Edr\Entity\Plaine\Plaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlaineOpenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'inscriptionOpen',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Ouvrir les inscriptions',
                    'help' => 'Si cette case est cochée, les parents pourront inscrire leurs enfants à la plaine',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Plaine::class,
            ]
        );
    }
}

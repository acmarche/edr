<?php

namespace AcMarche\Edr\Jour\Tarification\Form;

use AcMarche\Edr\Entity\Jour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class JourTarificationDegressiveWithForfaitType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    /**
     * @var string
     */
    private const LABEL = 'label';
    /**
     * @var string
     */
    private const HELP = 'help';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'prix1',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 1er enfant',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 2iem enfant',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix3',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix des suivants',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'forfait',
                MoneyType::class,
                [
                    'required' => true,
                    self::HELP => 'Forfait d’un euro de 12h15 à 13h30',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}

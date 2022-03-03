<?php

namespace AcMarche\Edr\Sante\Form\Etape;

use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Sante\Form\SanteReponseType;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SanteFicheEtape3Type extends AbstractType
{
    public function __construct(
        private SanteChecker $santeChecker
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'questions',
                CollectionType::class,
                [
                    'entry_type' => SanteReponseType::class,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => SanteFiche::class,
            ]
        );
    }
}

<?php

namespace AcMarche\Edr\Sante\Form;

use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Sante\Form\Etape\SanteFicheEtape2Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SanteFicheFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add(
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

    public function getParent(): ?string
    {
        return SanteFicheEtape2Type::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sante_fiche';
    }
}

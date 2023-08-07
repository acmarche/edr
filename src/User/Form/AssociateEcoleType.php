<?php

namespace AcMarche\Edr\User\Form;

use AcMarche\Edr\Ecole\Repository\EcoleRepository;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\User\Dto\AssociateUserEcoleDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssociateEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'ecoles',
                EntityType::class,
                [
                    'label' => 'Sélectionnez une ou plusieurs écoles',
                    'class' => Ecole::class,
                    'placeholder' => 'Sélectionnez',
                    'required' => true,
                    'query_builder' => static fn(EcoleRepository $cr) => $cr->findForAssociate(),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => AssociateUserEcoleDto::class,
            ]
        );
    }
}

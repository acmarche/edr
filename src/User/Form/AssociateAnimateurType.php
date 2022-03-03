<?php

namespace AcMarche\Edr\User\Form;

use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\User\Dto\AssociateUserAnimateurDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssociateAnimateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'animateur',
                EntityType::class,
                [
                    'label' => 'Sélectionnez un animateur',
                    'class' => Animateur::class,
                    'placeholder' => 'Sélectionnez l\'animateur',
                    'required' => true,
                    'query_builder' => fn (AnimateurRepository $cr) => $cr->findForAssociateAnimateur(),
                ]
            )
            ->add(
                'sendEmail',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Prévenir par email l\'animateur',
                    'help' => 'Un email va être envoyé à l\'animateur pour signaler que son compte a été associé à une fiche animateur',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => AssociateUserAnimateurDto::class,
            ]
        );
    }
}

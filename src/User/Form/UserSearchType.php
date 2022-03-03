<?php

namespace AcMarche\Edr\User\Form;

use AcMarche\Edr\Security\Role\EdrSecurityRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $roles = array_flip(EdrSecurityRole::ROLES);
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nom',
                    ],
                ]
            )
            ->add(
                'role',
                ChoiceType::class,
                [
                    'choices' => $roles,
                    'required' => false,
                    'placeholder' => 'Type de compte',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
            ]
        );
    }
}

<?php

namespace AcMarche\Edr\User\Form;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $roles = array_flip(EdrSecurityRole::ROLES);
        $formBuilder
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => $roles,
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}

<?php

namespace AcMarche\Edr\User\Form;

use AcMarche\Edr\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('plainPassword')
            ->remove('roles');
    }

    public function getParent(): ?string
    {
        return UserType::class;
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

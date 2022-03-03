<?php

namespace AcMarche\Edr\Registration\Form;

use AcMarche\Edr\User\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

final class RegistrationFormType extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $url = $this->router->generate('edr_front_modalite');

        $formBuilder
            ->remove('roles')
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Nom du parent',
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Prénom du parent',
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                    'required' => true,
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'label' => 'J\'accepte les conditions d\'utilisation',
                    'help_html' => true,
                    'help' => '<a href="'.$url.'" target="_blank">Lire les conditions d\'utilisation</a>',
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'You should agree to our terms.',
                            ]
                        ),
                    ],
                ]
            );
    }

    public function getParent(): ?string
    {
        return UserType::class;
    }
}

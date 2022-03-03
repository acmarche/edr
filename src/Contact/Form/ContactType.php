<?php

namespace AcMarche\Edr\Contact\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Votre nom',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['Votre email'],
                ]
            )
            ->add(
                'texte',
                TextareaType::class,
                [
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            );
    }
}

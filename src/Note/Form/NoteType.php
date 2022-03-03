<?php

namespace AcMarche\Edr\Note\Form;

use AcMarche\Edr\Entity\Note;
use AcMarche\Edr\Form\Type\ArchivedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'label' => 'Contenu',
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            )
            ->add('archived', ArchivedType::class);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Note::class,
            ]
        );
    }
}

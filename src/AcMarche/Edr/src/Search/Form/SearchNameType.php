<?php

namespace AcMarche\Edr\Search\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Nom',
                        'autocomplete' => 'off',
                        'data-autocomplete-target' => 'input',
                    ],
                ]
            );
    }
}

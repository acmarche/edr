<?php

namespace AcMarche\Edr\Page\Form;

use AcMarche\Edr\Entity\Page;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Titre',
                    'required' => true,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'menu',
                CheckboxType::class,
                [
                    'label' => 'Afficher dans le menu',
                    'required' => false,
                    'attr' => [
                        'help' => 'off',
                    ],
                ]
            )
            ->add(
                'content',
                CKEditorType::class,
                [
                    'required' => true,
                    'label' => 'Contenu',
                    'config_name' => 'acmarche_config',
                    'attr' => [
                        'cols' => 5,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Page::class,
            ]
        );
    }
}

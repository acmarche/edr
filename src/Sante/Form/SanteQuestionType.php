<?php

namespace AcMarche\Edr\Sante\Form;

use AcMarche\Edr\Entity\Sante\SanteQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SanteQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'complement',
                CheckboxType::class,
                [
                    'label' => 'Un complément d\'information est-il nécessaire ?',
                    'help' => 'Cochez la case si c\'est le cas. ',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'complement_label',
                TextType::class,
                [
                    'label' => 'Texte d\'aide pour le complément',
                    'help' => 'Par ex: Date de vaccin, quel type de médicaments...<br /><b>Ce champ ne devra être remplis par l\'utilisateur que si celui-ci à répondu oui à la question</b>',
                    'help_html' => true,
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => SanteQuestion::class,
            ]
        );
    }
}

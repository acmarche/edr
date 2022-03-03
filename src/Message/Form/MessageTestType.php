<?php

namespace AcMarche\Edr\Message\Form;

use AcMarche\Edr\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MessageTestType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'from',
                EmailType::class,
                [
                    'label' => 'De',
                ]
            )->add(
                'to',
                EmailType::class,
                [
                    'label' => 'A',
                ]
            )
            ->add(
                'sujet',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'texte',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'rows' => 10,
                        'cols' => 50,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Message::class,
            ]
        );
    }
}

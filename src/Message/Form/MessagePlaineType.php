<?php

namespace AcMarche\Edr\Message\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class MessagePlaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'attachCourriers',
                CheckboxType::class,
                [
                    'label' => 'Attacher les courriers ?',
                    'help' => 'Les courriers joints aux groupes scolaires seront joint au mail',
                    'required' => false,
                ]
            );
    }

    public function getParent(): ?string
    {
        return MessageType::class;
    }
}

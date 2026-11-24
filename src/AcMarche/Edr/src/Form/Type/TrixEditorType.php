<?php

namespace AcMarche\Edr\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Rich text (HTML) field edited with Trix, loaded by assets/controllers/trix_controller.js.
 * Rendered by templates/commun/form/trix_editor.html.twig.
 */
final class TrixEditorType extends AbstractType
{
    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'trix_editor';
    }
}

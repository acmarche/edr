<?php

namespace AcMarche\Edr\Organisation\Form;

use AcMarche\Edr\Entity\Organisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add('initiale')
            ->add('email')
            ->add('site_web')
            ->add('rue')
            ->add('code_postal')
            ->add('localite')
            ->add('telephone')
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Autre téléphone',
                ]
            )
            ->add('gsm')
            ->add('remarque')
            ->add(
                'photo',
                VichImageType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Organisation::class,
            ]
        );
    }
}

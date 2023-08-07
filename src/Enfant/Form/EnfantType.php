<?php

namespace AcMarche\Edr\Enfant\Form;

use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Scolaire\AnneeScolaire;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Form\Type\OrdreType;
use AcMarche\Edr\Form\Type\RemarqueType;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class EnfantType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $year = new DateTime('today');
        $year = $year->format('Y');

        $isAdmin = !$this->security->isGranted(EdrSecurityRole::ROLE_ADMIN);

        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'birthday',
                BirthdayType::class,
                [
                    'label' => 'Né le',
                    'required' => $isAdmin,
                    'years' => range($year - 15, $year),
                ]
            )
            ->add(
                'registre_national',
                TextType::class,
                [
                    'label' => 'Numéro national',
                    'required' => false,
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    'required' => $isAdmin,
                    'choices' => EdrConstantes::SEXES,
                    'placeholder' => 'Choisissez son sexe',
                ]
            )
            ->add(
                'poids',
                TextType::class,
                [
                    'label' => 'Poids',
                    'help' => 'en kg',
                    'required' => false,
                ]
            )
            ->add(
                'ordre',
                OrdreType::class,
                [
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => $isAdmin,
                    'placeholder' => 'Choisissez son école',
                ]
            )
            ->add(
                'annee_scolaire',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'required' => false,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                ]
            )
            ->add(
                'groupe_scolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    'required' => false,
                    'label' => 'Forcer le groupe scolaire',
                    'placeholder' => 'Choisissez un groupe',
                    'help' => 'Utilisé pour le listing des présences',
                ]
            )
            ->add(
                'remarque',
                RemarqueType::class
            )
            ->add(
                'photoAutorisation',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Autorisation de diffusion de ses photos',
                    'help' => "Cochez si vous autorisez la diffusion des photos de l'enfant",
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'archived',
                CheckboxType::class,
                [
                    'label' => 'Archiver',
                    'help' => 'Ces données seront toujours visibles, mais il ne pourra plus être inscrit nul part',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'accueilEcole',
                CheckboxType::class,
                [
                    'label' => 'Accueils des écoles',
                    'required' => false,
                    'help' => 'L\'enfant vient-il en accueil dans les écoles ?',
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
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
                'data_class' => Enfant::class,
            ]
        );
    }
}

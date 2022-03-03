<?php

namespace AcMarche\Edr\Security\Voter;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
final class AccueilVoter extends Voter
{
    public const ADD = 'accueil_new';
    public const SHOW = 'accueil_show';
    public const EDIT = 'accueil_edit';
    public const DELETE = 'accueil_delete';
    public ?Tuteur $tuteurOfUser = null;
    private ?UserInterface $user = null;
    private Enfant $enfant;

    public function __construct(
        private Security $security,
        private RelationRepository $relationRepository,
        private TuteurUtils $tuteurUtils
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Accueil && \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute($attribute, $accueil, TokenInterface $token): bool
    {
        if (! $token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();

        if ($this->security->isGranted(EdrSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        $this->enfant = $accueil->getEnfant();

        if ($this->security->isGranted(EdrSecurityRole::ROLE_PARENT)) {
            return $this->checkTuteur();
        }

        if ($this->security->isGranted(EdrSecurityRole::ROLE_ECOLE)) {
            return $this->checkEcoles();
        }

        return false;
    }

    private function checkEcoles(): bool
    {
        $ecoles = $this->user->getEcoles();

        if (0 === (is_countable($ecoles) ? \count($ecoles) : 0)) {
            return false;
        }

        return $ecoles->contains($this->enfant->getEcole());
    }

    private function checkTuteur(): bool
    {
        $this->tuteurOfUser = $this->tuteurUtils->getTuteurByUser($this->user);

        if (! $this->tuteurOfUser instanceof Tuteur) {
            return false;
        }

        $relations = $this->relationRepository->findByTuteur($this->tuteurOfUser);

        $enfants = array_map(
            fn ($relation) => $relation->getEnfant()->getId(),
            $relations
        );

        return \in_array($this->enfant->getId(), $enfants, true);
    }
}

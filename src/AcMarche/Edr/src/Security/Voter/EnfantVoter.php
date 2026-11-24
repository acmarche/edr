<?php

namespace AcMarche\Edr\Security\Voter;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 */
final class EnfantVoter extends Voter
{
    public const ADD = 'enfant_new';

    public const SHOW = 'enfant_show';

    public const EDIT = 'enfant_edit';

    public const DELETE = 'enfant_delete';

    private UserInterface $user;

    private Enfant $enfant;

    public function __construct(
        private readonly RelationRepository $relationRepository,
        private readonly TuteurUtils $tuteurUtils,
        private readonly \Symfony\Bundle\SecurityBundle\Security $security
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Enfant) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $enfant, TokenInterface $token, ?Vote $vote = null): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();
        $this->enfant = $enfant;

        if ($this->security->isGranted(EdrSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        return match ($attribute) {
            self::SHOW => $this->canView(),
            self::ADD => $this->canAdd(),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            default => false,
        };
    }

    private function canView(): bool
    {
        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        return $this->security->isGranted(EdrSecurityRole::ROLE_PARENT) && $this->checkTuteur();
    }

    private function canAdd(): bool
    {
        return false; //not use
    }

    private function canDelete(): bool
    {
        return false; //only admin
    }

    private function checkTuteur(): bool
    {
        if (!$this->security->isGranted(EdrSecurityRole::ROLE_PARENT)) {
            return false;
        }

        $tuteur = null;
        $tuteur = $this->tuteurUtils->getTuteurByUser($this->user);
        if (!$tuteur instanceof Tuteur) {
            return false;
        }

        $relations = $this->relationRepository->findByTuteur($tuteur);

        $enfants = array_map(
            static fn ($relation) => $relation->getEnfant()->getId(),
            $relations
        );

        return \in_array($this->enfant->getId(), $enfants, true);
    }
}

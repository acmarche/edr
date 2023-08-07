<?php

namespace AcMarche\Edr\Security\Voter;

use AcMarche\Edr\Entity\Tuteur;
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
 */
final class TuteurVoter extends Voter
{
    public const ADD = 'tuteur_new';

    public const SHOW = 'tuteur_show';

    public const EDIT = 'tuteur_edit';

    public const DELETE = 'tuteur_delete';

    private UserInterface $user;

    private ?Tuteur $tuteurOfUser = null;

    private ?Tuteur $tuteurToCheck = null;

    public function __construct(
        private readonly Security $security,
        private readonly TuteurUtils $tuteurUtils
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Tuteur) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute($attribute, $tuteur, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();

        $this->tuteurToCheck = $tuteur;

        if ($this->security->isGranted(EdrSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        $this->tuteurOfUser = $this->tuteurUtils->getTuteurByUser($this->user);

        return match ($attribute) {
            self::SHOW => $this->canShow(),
            self::ADD => $this->canAdd(),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            default => false,
        };
    }

    private function canShow(): bool
    {
        if ($this->security->isGranted(EdrSecurityRole::ROLE_ANIMATEUR)) {
            return true;
        }

        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        return $this->checkOwnTuteur();
    }

    private function canAdd(): bool
    {
        return (bool) $this->canEdit();
    }

    private function canDelete(): bool
    {
        return (bool) $this->canEdit();
    }

    private function checkOwnTuteur(): bool
    {
        if (!$this->security->isGranted(EdrSecurityRole::ROLE_PARENT)) {
            return false;
        }

        if (!$this->tuteurOfUser instanceof Tuteur) {
            return false;
        }

        if (!$this->tuteurToCheck instanceof Tuteur) {
            return false;
        }

        return $this->tuteurOfUser->getId() === $this->tuteurToCheck->getId();
    }
}

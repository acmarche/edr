<?php

namespace AcMarche\Edr\Security\Voter;

use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use Doctrine\Common\Collections\ArrayCollection;
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
final class JourVoter extends Voter
{
    public const ADD = 'jour_new';

    public const SHOW = 'jour_show';

    public const EDIT = 'jour_edit';

    public const DELETE = 'jour_delete';

    private ?UserInterface $user = null;

    private Jour $jour;

    /**
     * @var Jour[]|ArrayCollection
     */
    private iterable $jours;

    private ?Animateur $animateur = null;

    public function __construct(
        public RelationRepository $relationRepository,
        private readonly \Symfony\Bundle\SecurityBundle\Security $security
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Jour) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute($attribute, $jour, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();

        $this->jour = $jour;

        if ($this->security->isGranted(EdrSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        if (!$this->security->isGranted(EdrSecurityRole::ROLE_ANIMATEUR)) {
            return false;
        }

        if (!$this->checkJoursAnimateur()) {
            return false;
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
        return $this->jours->contains($this->jour);
    }

    private function canEdit(): bool
    {
        return false; //not use
    }

    private function canAdd(): bool
    {
        return false; //not use
    }

    private function canDelete(): bool
    {
        return false; //only admin
    }

    private function checkJoursAnimateur(): bool
    {
        $this->animateur = $this->user->getAnimateur();

        if (!$this->animateur instanceof Animateur) {
            return false;
        }

        $this->jours = $this->animateur->getJours();

        return 0 !== \count($this->jours);
    }
}

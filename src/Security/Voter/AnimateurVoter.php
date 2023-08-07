<?php

namespace AcMarche\Edr\Security\Voter;

use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use Doctrine\Common\Collections\Collection;
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
final class AnimateurVoter extends Voter
{
    public const INDEX = 'animateur_index';

    public const SHOW = 'animateur_show';

    public const ADD = 'animateur_add';

    public const EDIT = 'animateur_edit';

    public const DELETE = 'animateur_delete';

    public UserInterface $user;

    private ?Animateur $animateur = null;

    /**
     * @var Animateur[]|Collection
     */
    private array|Collection $animateurs;

    public function __construct(
        private readonly Security $security
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        //a cause de index pas d'animateur defini
        if ($subject && ! $subject instanceof Animateur) {
            return false;
        }

        return \in_array($attribute, [self::INDEX, self::SHOW, self::ADD, self::EDIT, self::DELETE], true);
    }

    protected function voteOnAttribute($attribute, $animateur, TokenInterface $token): bool
    {
        if (! $token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();

        if ($this->security->isGranted(EdrSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        if (! $this->security->isGranted(EdrSecurityRole::ROLE_ANIMATEUR)) {
            return false;
        }

        $this->animateur = $animateur;
        $this->animateurs = $this->user->getAnimateurs();

        return match ($attribute) {
            self::INDEX => $this->canIndex(),
            self::SHOW => $this->canView(),
            self::DELETE, self::ADD => false,
            self::EDIT => $this->canEdit(),
            default => false,
        };
    }

    private function canIndex(): bool
    {
        return $this->checkAnimateurs();
    }

    private function canView(): bool
    {
        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        if (! $this->checkAnimateurs()) {
            return false;
        }

        if (!$this->animateur instanceof Animateur) {
            return false;
        }

        return $this->animateurs->contains($this->animateur);
    }

    private function checkAnimateurs(): bool
    {
        return 0 !== \count($this->animateurs);
    }
}

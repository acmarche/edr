<?php

namespace AcMarche\Edr\Animateur\Message;

final readonly class AnimateurDeleted
{
    public function __construct(
        private int $animateurId
    ) {
    }

    public function getAnimateurId(): int
    {
        return $this->animateurId;
    }
}

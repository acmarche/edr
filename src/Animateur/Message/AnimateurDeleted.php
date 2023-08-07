<?php

namespace AcMarche\Edr\Animateur\Message;

final class AnimateurDeleted
{
    public function __construct(
        private readonly int $animateurId
    ) {
    }

    public function getAnimateurId(): int
    {
        return $this->animateurId;
    }
}

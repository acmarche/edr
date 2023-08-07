<?php

namespace AcMarche\Edr\Animateur\Message;

final class AnimateurCreated
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

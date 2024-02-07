<?php

namespace AcMarche\Edr\Twig;

use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: '@AcMarcheEdr/components/SearchLiveEnfant.html.twig')]
class SearchLiveEnfant
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;

    public function __construct(private readonly EnfantRepository $enfantRepository)
    {
    }

    public function getEnfants(): array
    {
        if (null !== $this->query) {
            return $this->enfantRepository->findByName($this->query);
        }

        return [];
    }
}

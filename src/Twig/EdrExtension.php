<?php

namespace AcMarche\Edr\Twig;

use AcMarche\Edr\Data\EdrConstantes;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class EdrExtension extends AbstractExtension
{
    /**
     * @var string[]
     */
    private const MONTHS = [
        1 => 'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',
    ];

    public function getFilters(): array
    {
        return [
            new TwigFilter('edr_month_fr', fn (int $number) => $this->monthFr($number)),
            new TwigFilter('edr_absence_text', fn ($number): string => $this->absenceFilter($number)),
        ];
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('inIds', fn (int $number, array $objects) => $this->inIds($number, $objects))];
    }

    public function absenceFilter($number): string
    {
        return EdrConstantes::getAbsenceTxt($number);
    }

    public function monthFr(int $number): int|string
    {
        return self::MONTHS[$number] ?? $number;
    }

    private function inIds(int $number, array $objects): bool
    {
        $ids = array_map(fn ($object) => $object->getId(), $objects);

        return \in_array($number, $ids, true);
    }
}

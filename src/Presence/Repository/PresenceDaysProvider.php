<?php

namespace AcMarche\Edr\Presence\Repository;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use AcMarche\Edr\Utils\SortUtils;

final readonly class PresenceDaysProvider implements PresenceDaysProviderInterface
{
    public function __construct(
        private JourRepository $jourRepository,
        private PresenceUtils $presenceUtils
    ) {
    }

    /**
     * @return Jour[]
     */
    public function getAllDaysToSubscribe(Enfant $enfant): array
    {
        $deadLineDatePresence = $this->presenceUtils->getDeadLineDatePresence();
        $jours = $this->jourRepository->findJourNotPedagogiqueByDateGreatherOrEqualAndNotRegister($deadLineDatePresence, $enfant);

        $deadLineDatePedagogique = $this->presenceUtils->getDeadLineDatePedagogique();
        $pedagogiques = $this->jourRepository->findPedagogiqueByDateGreatherOrEqualAndNotRegister($deadLineDatePedagogique, $enfant);

        $all = array_merge($jours, $pedagogiques);

        return SortUtils::sortJoursByDateTime($all);
    }
}

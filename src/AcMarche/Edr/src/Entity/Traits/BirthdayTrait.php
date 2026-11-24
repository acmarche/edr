<?php

namespace AcMarche\Edr\Entity\Traits;

use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait BirthdayTrait
{
    /**
     * @var DateTime|null
     */
    #[ORM\Column(name: 'birthday', type: 'date', nullable: true)]
    private ?DateTimeInterface $birthday = null;

    /**
     * @return DateTime|DateTimeImmutable|null
     */
    public function getBirthday(): ?DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param DateTime|DateTimeImmutable|null $birthday
     */
    public function setBirthday(?DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param bool $rounded arrondi à 0.5
     */
    public function getAge(?DateTimeInterface $dateReference = null, bool $rounded = false): ?float
    {
        $birthday = $this->birthday;

        if (!$birthday) {
            return null;
        }

        $today = Carbon::now();

        if ($dateReference instanceof DateTimeInterface) {
            $today = Carbon::instance($dateReference);
        }

        $age = (float) Carbon::parse($birthday)->diff($today)->format('%y.%m');
        if ($rounded) {
            return floor($age * 2) / 2;
        }

        return $age;
    }

    /**
     * alternative.
     */
    public function getAge2(): int
    {
        $daysSinceEpoch = Carbon::createFromDate(1975, 5, 21)->diffInDays();
        $howOldAmI = Carbon::createFromDate(1975, 5, 21)->age;
    }
}

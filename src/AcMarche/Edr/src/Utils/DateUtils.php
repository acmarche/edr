<?php

namespace AcMarche\Edr\Utils;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use IntlDateFormatter;
use Locale;
use Twig\Environment;

final readonly class DateUtils
{
    public function __construct(
        private Environment $environment
    ) {
    }

    /**
     * @param string $mois 05/2020
     *
     * @return DateTime|DateTimeImmutable
     * @throws Exception
     *
     */
    public static function createDateTimeFromDayMonth(string $mois): DateTime
    {
        if ($date = DateTime::createFromFormat('d/m/Y', '01/' . $mois)) {
            return $date;
        }

        throw new Exception('Mauvais format de date: ' . $mois);
    }

    public static function formatFr(DateTimeInterface $dateTime, ?int $format = IntlDateFormatter::FULL): bool|string
    {
        $intlDateFormatter = new IntlDateFormatter(
            Locale::getDefault(),
            $format,
            IntlDateFormatter::NONE,
            new DateTimeZone('Europe/Brussels'),
            IntlDateFormatter::GREGORIAN
        );

        return $intlDateFormatter->format($dateTime);
    }

    /**
     * @param DateTime $dateTime "01/08/2018"
     */
    public static function getDatePeriod(DateTimeInterface $dateTime): DatePeriod
    {
        $begin = DateTimeImmutable::createFromMutable($dateTime);
        $end = $begin->modify('last day of this month');
        $end = $end->modify('+1 day');

        $dateInterval = new DateInterval('P1D');

        return new DatePeriod($begin, $dateInterval, $end);
    }

    /**
     * @param DateTime $dateTime "01/08/2018"
     */
    public static function getAllDaysOfMonth(DateTimeInterface $dateTime): DatePeriod
    {
        $begin = DateTimeImmutable::createFromMutable($dateTime);
        $start = $begin->modify('first day of this month');
        $end = $begin->modify('last day of this month');

        $dateInterval = new DateInterval('P1D');

        return new DatePeriod($start, $dateInterval, $end);
    }

    public function renderMonth(Ecole $ecole, string $heure, int $weekSelected, CarbonInterface $date): string
    {
        $weeks = $this->getWeeksOfMonth($date);
        $previous = $date->copy()->subMonth();
        $next = $date->copy()->addMonth();

        return $this->environment->render(
            '@AcMarcheEdr/commun/calendar/_month.html.twig',
            [
                'weeks' => $weeks,
                'ecole' => $ecole,
                'heure' => $heure,
                'date' => $date,
                'previous' => $previous,
                'next' => $next,
                'weekSelected' => $weekSelected,
            ]
        );
    }

    public function getWeeksOfMonth(CarbonInterface $date): array
    {
        $weeks = [];
        $firstDay = $date->firstOfMonth();

        $firstDayWeek = $firstDay->copy()->startOfWeek()->toMutable();

        do {
            $weeks[] = $this->getWeekOfMonth($firstDayWeek); // point at end ofWeek
            $firstDayWeek->nextWeekday();
        } while ($firstDayWeek->isSameMonth($firstDay));

        return $weeks;
    }

    public function getWeekOfMonth(CarbonInterface $carbon): CarbonPeriod
    {
        $debut = $carbon->toDateString();
        $fin = $carbon->endOfWeek()->toDateString();

        return Carbon::parse($debut)->daysUntil($fin);
    }

    public function getWeekByNumber(CarbonInterface $date, int $week): CarbonPeriod
    {
        $date->week($week);

        return Carbon::parse($date->startOfWeek())->daysUntil($date->endOfWeek());
    }

    public function createDateImmutableFromYearWeek(int $year, int $week): CarbonImmutable
    {
        $date = Carbon::create($year);
        $date->setISODate($year, $week);
        $date->locale('fr');

        return $date->toImmutable();
    }

    public function createDateImmutableFromYearMonth(int $year, int $month): CarbonImmutable
    {
        return CarbonImmutable::create($year, $month, 01)->locale('fr');
    }

    public static function getMonths(DateTimeInterface $dateBegin): CarbonPeriod
    {
        $date = Carbon::instance($dateBegin);
        $today = Carbon::instance(new DateTime());

        $firstDayOfWeek = $date->toMutable()->toDateString();
        $lastDayOffWeek = $today->toDateString(); //+6

        return Carbon::parse($firstDayOfWeek)->monthsUntil($lastDayOffWeek);
    }

    /**
     * @param Enfant[] $enfants
     */
    public static function hasTwins(array $enfants): bool
    {
        $birthdays = [];
        foreach ($enfants as $enfant) {
            if (($birthday = $enfant->getBirthday()) !== null) {
                $birthdays[] = $birthday->format('Y-m-d');
            }
        }

        return \count($birthdays) !== \count(array_unique($birthdays));
    }

    public static function dayIsWeek(DateTimeInterface $date): bool
    {
        return $date->format('N') < 6;
    }
}

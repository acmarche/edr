<?php

namespace AcMarche\Edr\Tests\Calculator;

use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorHottonTest extends KernelTestCase
{
    public function testAbsent(): void
    {
        $calculator = $this->getCalculator();
        $tuteur = new Tuteur();
        $enfant = new Enfant();
        $jour = new Jour();

        $presence = new Presence($tuteur, $enfant, $jour);
        $presence->setAbsent(EdrConstantes::ABSENCE_AVEC_CERTIF);
        self::assertSame(0.0, $calculator->calculate($presence));
    }

    public function testOrdre(): void
    {
        $calculator = $this->getCalculator();
        $jour = new Jour(new DateTime());
        $jour->setPrix1(3);
        $jour->setPrix2(2);
        $jour->setPrix3(1);

        $presence = new Presence(new Tuteur(), new Enfant(), $jour);

        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 0));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 1));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 2));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 3));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 4));
    }

    public function testPedagogique()
    {
        $calculator = $this->getCalculator();
        $jour = new Jour(new DateTime());
        $jour->setPedagogique(true);
        $jour->setPrix1(3);
        $jour->setPrix2(2);
        $jour->setPrix3(1);

        $presence = new Presence(new Tuteur(), new Enfant(), $jour);
        $presence->setHalf(true);
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 0));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 1));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 2));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 3));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 4));
        $presence->setHalf(false);
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 0));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 1));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 2));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 3));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 4));
    }

    private function getCalculator(): PresenceCalculatorInterface
    {
        $container = static::getContainer();

        return $container->get(PresenceCalculatorInterface::class);
    }
}

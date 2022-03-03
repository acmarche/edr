<?php

namespace AcMarche\Edr\Tests\Communication;

use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\Factory\CommunicationFactoryHotton;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommunicationFactoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        //$routerService = self::$container->get('router');
        //$myCustomService = self::$container->get(CustomService::class);
        $container = static::getContainer();
        $communicationFactory = $container->get(CommunicationFactoryHotton::class);
        $facture = new Facture(new Tuteur());
        $id = ' ';//je ne sais pas faire $facture->setId()
        $facture->setMois('09-2021');
        $ecole = new Ecole();
        $ecole->setNom('Hotton');
        $ecole->setAbreviation('Hot');
        $facture->ecolesListing = [$ecole];
        $year = 2021;
        $short = substr($year, -2, 2);
        self::assertSame('21', $short);
        $communication = $communicationFactory->generateForPresence($facture);

        self::assertSame('Hot'.' '.$id.' '.$facture->getMois(), $communication);
    }
}

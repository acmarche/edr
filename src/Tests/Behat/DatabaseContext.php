<?php

namespace AcMarche\Edr\Tests\Behat;

use AcMarche\Edr\Fixture\FixtureLoader;
use Behat\Behat\Context\Context;

class DatabaseContext implements Context
{
    public function __construct(
        private readonly FixtureLoader $fixtureLoader
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function loadFixtures(): void
    {
        $this->fixtureLoader->load();
    }

    /**
     * @AfterScenario
     */
    public function rollbackPostgreSqlTransaction(): void
    {
    }
}

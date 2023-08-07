<?php

namespace AcMarche\Edr\Tests\Behat;

use AcMarche\Edr\Utils\DateUtils;
use Behat\MinkExtension\Context\MinkContext;
use Carbon\Carbon;
use Exception;

class FeatureContext extends MinkContext
{
    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin(): void
    {
        $this->visitPath('/login');
        //var_dump($this->getSession()->getPage()->getContent());
        $this->fillField('username', 'jf@marche.be');
        $this->fillField('password', 'homer');
        $this->pressButton('Me connecter');
    }

    /**
     * Given I am logged in as user :username.
     *
     * @Given /^I am logged in as user "([^"]*)"$/
     */
    public function iAmLoggedInAsUser(string $username): void
    {
        $this->getSession()->visit('/login');
        $this->fillField('username', $username);
        $this->fillField('password', 'homer');
        $this->pressButton('Me connecter');
    }

    /**
     * @When /^I am login with user "([^"]*)" and password "([^"]*)"$/
     */
    public function iAmLoginWithUserAndPassword(string $email, string $password): void
    {
        $this->getSession()->visit('/login');
        $this->fillField('username', $email);
        $this->fillField('password', $password);
        $this->pressButton('Me connecter');
    }

    /**
     * @When /^I select day plus "(\d+)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function iSelectDayPlusFrom($nbDays, $select): void
    {
        $today = Carbon::today();
        $today->addDays($nbDays);
        $today = ucfirst(DateUtils::formatFr($today));

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($today);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @When /^I additionally select day plus "(\d+)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function iAdditionallySelectDayPlusFrom($nbDays, $select): void
    {
        $today = Carbon::today();
        $today->addDays($nbDays);
        $today = ucfirst(DateUtils::formatFr($today));

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($today);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    /**
     * @Then /^I should see "([^"]*)" exactly "([^"]*)" times$/
     *
     * @throws Exception
     */
    public function iShouldSeeTextSoManyTimes($sText, $iExpected): void
    {
        $sContent = $this->getSession()->getPage()->getText();
        $iFound = substr_count((string) $sContent, (string) $sText);
        if ($iExpected !== $iFound) {
            throw new Exception('Found ' . $iFound . ' occurences of "' . $sText . '" when expecting ' . $iExpected);
        }
    }
}

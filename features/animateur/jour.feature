Feature: Gestion de sa fiche animateur
  Je suis connecté
  Je fais une recherche

  Background:
    Given I am login with user "kevin@marche.be" and password "homer"
    Given I am on "/animateur/presence/"

  Scenario:  Page index
    Then I should see "Listing des présences"
    Then I should see "Edr 6 Mai 2020"
    Then I should see "Jeudi 19 Septembre 2024"

  Scenario:  Je consulte une date
    Then I follow "Edr 6 Mai 2020"
    Then I should see "SIMPSON"
    Then I should see "Bart"
    Then I should not see "PERET Merlin"

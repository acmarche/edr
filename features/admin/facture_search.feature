Feature: Gestion des factures
  Je suis connecté
  Je consulte une facture
  Je cherche une facture

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I should see "Simpson"

  Scenario: Je consulte une facture
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "06-2020"
    Then I follow "06-2020"
    Then I should see "edr 6 mai 2020"
    Then I should see "25,50 €"

  Scenario: Je cherche une facture
    Given I am on "/admin/facture/search"
    And I fill in "facture_search[tuteur]" with "simps"
    And I fill in "facture_search[mois]" with "06-2020"
    And I select "Non payée" from "facture_search[paye]"
    And I press "Rechercher"
    Then I should see "06-2020"
    Then I should see "SIMPSON Homer"

Feature: Gestion des présences
  Rechercher par mois
  Rechercher par mois mauvaise date
  Rechercher par listing

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/presence/by/month"
    Then I should see "Liste des présences par mois"

  Scenario: Rechercher par mois
    Then I fill in "search_presence_by_month[mois]" with "09/2024"
    And I press "Rechercher"
    Then I should see "PERET Merlin"
    Then I should see "FERNANDEL Yves"
    Then I should see "SIMPSON Lisa"
    Then I should see "04/09/2024"
    Then I should see "11/09/2024"
    Then I should see "19/09/2024"
    Then I should see "PERET Merlin"
    Then I should see "FERNANDEL Yves"
    Then I should see "SIMPSON Lisa"
    And I follow "Par défaut"
    Then the response status code should be 200

  Scenario: Rechercher par mois mauvaise date
    Then I fill in "search_presence_by_month[mois]" with "09 2024"
    And I press "Rechercher"
    Then I should see "Mauvais format de date"

  Scenario: Rechercher présences
    Given I am on "/admin/presence/"
    Then I should see "Liste des présences"
    Then I select "Jeudi 19 septembre 2024" from "search_presence[jour]"
    Then I select "Aye" from "search_presence[ecole]"
    And I press "Rechercher"
    Then I should see "PERET Merlin"
    Then I should see "Moyens"
    Then I should not see "FERNANDEL Yves"
    Then I should not see "SIMPSON Lisa"
    Then I should see "19 septembre 2024"

  Scenario: Export xls
    Given I am on "/admin/presence/"
    Then I should see "Liste des présences"
    Then I select "Jeudi 19 septembre 2024" from "search_presence[jour]"
    And I press "Rechercher"
    Then I follow "export_xls_presence"
    Then the response status code should be 200

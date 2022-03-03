Feature: Gestion des factures
  Je suis connecté
  Je crée une créance

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I should see "Simpson"
    Then I follow "Simpson"
    Then I follow "Ses créances"

  Scenario: Je crée une créance
    Then I follow "Nouvelle créance"
    And I fill in "creance[nom]" with "Absence a rembourser"
    And I fill in "creance[montant]" with "20"
    And I fill in "creance[dateLe]" with "2021-10-05"
    And I press "Sauvegarder"
    Then I should see "La créance a bien été ajoutée"
    Then I should see "mardi 5 octobre 2021"
    Then I should see "20,00 €"


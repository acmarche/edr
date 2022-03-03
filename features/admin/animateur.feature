Feature: Gestion des animateurs
  Je suis connecté
  J' ajoute le animateur
  J' édite le animateur
  Je supprime l' animateur
  Ses jours de travail animateur

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/animateur/"
    Then I should see "Liste des animateurs"
    Then I fill in "search_animateur[nom]" with "Moe"
    And I press "Rechercher"
    Then I should see "Moe"

  Scenario: Ajout un animateur
    Then I follow "Un animateur"
    And I fill in "animateur[nom]" with "Marchal"
    And I fill in "animateur[prenom]" with "Joseph"
    And I fill in "animateur[rue]" with "Rue des Dentelles"
    And I fill in "animateur[code_postal]" with "6900"
    And I fill in "animateur[localite]" with "Marloie"
    And I fill in "animateur[telephone]" with "047 58 99 66"
    And I fill in "animateur[email]" with "joseph@domain.be"
    And I press "Sauvegarder"
    Then I should see "MARCHAL Joseph"
    Then I should see "Rue des Dentelles"

  Scenario: Modifier un animateur
    Then I follow "Szyslak"
    Then I follow "Modifier"
    And I fill in "animateur[gsm]" with "0476 22 66 99"
    And I select "Masculin" from "animateur_sexe"
    And I press "Sauvegarder"
    Then I should see "0476 22 66 99"

  Scenario: Ses jours de garde
    Then I follow "Szyslak"
    Then I follow "Ses jours de travails"
    And I check "Dimanche 25 octobre 2020"
    And I check "Edr 4 septembre 2024"
    And I press "Sauvegarder"
    Then I should see "dimanche 25 octobre 2020"
    Then I should see "edr 4 septembre 2024"

  Scenario: Supprimer un animateur
    Then I fill in "search_animateur[nom]" with "Barney"
    And I press "Rechercher"
    Then I should see "Gumble"
    Then I follow "Gumble"
    Then I press "Supprimer l' animateur"
   # Then print last response
    Then I should see "L' animateur a bien été supprimé"

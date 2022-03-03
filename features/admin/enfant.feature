Feature: Gestion des enfants
  Je suis connecté
  J' ajoute un enfant
  J' édite un enfant
  Je supprime un enfant sans tuteur
  Je supprime un enfant avec tuteur
  Je fais une recherche complète

  Background:
    Given I am logged in as an admin

  Scenario: Ajout un enfant
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "ajouter un nouvel enfant"
    Then I should see "Nouvel enfant"
    And I fill in "enfant[nom]" with "Funes"
    And I fill in "enfant[prenom]" with "Jules"
    And I select "6" from "enfant[birthday][day]"
    And I select "déc." from "enfant[birthday][month]"
    And I select "2015" from "enfant[birthday][year]"
    And I select "Waha" from "enfant[ecole]"
    And I select "3M" from "enfant[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "FUNES Jules"
    Then I should see "Waha"

  Scenario: Modifier un enfant
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Modifier"
    And I select "15" from "enfant[birthday][day]"
    And I select "mars" from "enfant[birthday][month]"
    And I select "2015" from "enfant[birthday][year]"
    And I press "Sauvegarder"
    Then I should see "15 mars 2015"

  Scenario: Supprimer un enfant sans tuteur
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Gauthie"
    And I press "Rechercher"
    Then I should see "Gauthie"
    Then I follow "Gauthie"
    Then I press "Supprimer l'enfant"
   # Then print last response
    Then I should see "L'enfant a bien été supprimé"

  Scenario: Supprimer un enfant avec tuteur et sante fiche
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I press "Supprimer l'enfant"
    Then I should see "L'enfant a bien été supprimé"

  Scenario: Rechercher un enfant
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Peret"
    Then I select "Aye" from "search_enfant[ecole]"
    Then I select "1P" from "search_enfant[annee_scolaire]"
    And I press "Rechercher"
    Then I should see "Peret"

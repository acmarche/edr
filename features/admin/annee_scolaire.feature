Feature: Gestion des années scolaires
  Je suis connecté
  J' ajoute une année scolaire
  J' édite une année scolaire
  Je supprime une année scolaire sans enfants
  Je ne peux pas supprimer une année scolaire avec enfants

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/annee_scolaire/"
    Then I should see "Liste des années scolaires"

  Scenario: Ajout une année scolaire
    Then I follow "Ajouter une année scolaire"
    And I fill in "annee_scolaire[nom]" with "7P"
    And I fill in "annee_scolaire[ordre]" with "12"
    And I select "1M" from "annee_scolaire[annee_suivante]"
    And I press "Sauvegarder"
    Then I should see "12"
    Then I should see "7P"

  Scenario: Modifier une année scolaire
    Then I follow "3M"
    Then I follow "Modifier"
    And I fill in "annee_scolaire[nom]" with "3 maternelle"
    And I press "Sauvegarder"
    Then I should see "3 maternelle"

  Scenario: Supprimer une année scolaire
    Then I follow "4P"
    Then I press "Supprimer l'année scolaire"
   # Then print last response
    Then I should see "L'année a bien été supprimée"

  Scenario: Supprimer une année scolaire avec enfants
    Then I follow "2M"
    Then I press "Supprimer l'année scolaire"
   # Then print last response
    Then I should see "Une année scolaire contenant des enfants ne peux pas être supprimée"

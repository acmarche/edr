Feature: Gestion des groupes scolaires
  Je suis connecté
  J' ajoute un groupe
  J' édite un groupe
  Je supprime un groupe avec enfant

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/groupe_scolaire/"
    Then I should see "Liste des groupes"

  Scenario: Ajout un groupe
    Then I follow "Ajouter un groupe"
    And I fill in "groupe_scolaire[nom]" with "Super héros"
    And I fill in "groupe_scolaire[age_minimum]" with "3"
    And I fill in "groupe_scolaire[age_maximum]" with "12"
    And I press "Sauvegarder"
    Then I should see "Le groupe a bien été ajouté"
    And I should see "3 ans"
    And I should see "12 ans"

  Scenario: Ajout un groupe pour plaine
    Then I follow "Ajouter un groupe"
    And I fill in "groupe_scolaire[nom]" with "Super héros"
    And I fill in "groupe_scolaire[age_minimum]" with "3"
    And I fill in "groupe_scolaire[age_maximum]" with "12"
    And I press "Sauvegarder"
    Then I should see "Le groupe a bien été ajouté"
    And I should see "3 ans"
    And I should see "12 ans"

  Scenario: Modifier un groupe
    Then I follow "Petits"
    Then I follow "Modifier"
    And I fill in "groupe_scolaire[nom]" with "Petites"
    And I press "Sauvegarder"
    Then I should see "Petites"

  Scenario: Supprimer un groupe
    Then I follow "Moyens"
    Then I press "Supprimer le groupe scolaire"
   # Then print last response
    Then I should see "Le groupe a bien été supprimé"

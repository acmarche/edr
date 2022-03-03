Feature: Gestion des réductions
  Je suis connecté
  J' ajoute la réduction "Handicap" pour un pourcentage de "66%"
  J' édite la réduction "Cpas" pour "88%"
  Je supprime la réduction "Cpas"

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/reduction/"
    Then I should see "Liste des réductions"

  Scenario: Ajout réduction
    Then I follow "Ajouter une réduction"
    And I fill in "reduction[nom]" with "Handicap"
    And I fill in "reduction[pourcentage]" with "66"
    And I press "Sauvegarder"
    Then I should see "66%"

  Scenario: Modifier une réduction
    Then I follow "Cpas"
    Then I follow "Modifier"
    And I fill in "reduction[pourcentage]" with "88"
    And I press "Sauvegarder"
    Then I should see "88%"

  Scenario: Supprimer une réduction
    Then I follow "Cpas"
    Then I press "Supprimer la réduction"
    #Then print last response
    Then I should see "La réduction a bien été supprimée"

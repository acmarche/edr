Feature: Gestion des documents
  Je suis connecté
  J' ajoute un document à une page
  Je modifie un document
  Je supprime un document

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/page/"
    Then I should see "Liste des pages"

  Scenario: Ajouter un document
    Then I follow "Page exemple"
    Then I follow "Ajouter un document"
    And I fill in "document[nom]" with "Règlement"
    When I attach the file "image/test.jpg" to "document[file][file]"
    And I press "Sauvegarder"
    Then I should see "Règlement"

  Scenario: Modifier un document
    Then I follow "Nous contacter"
    Then I follow "Règlement"
    Then I follow "Modifier"
    And I fill in "document[nom]" with "Règlementation"
    And I press "Sauvegarder"
    Then I should see "Règlementation"

  Scenario: Supprimer un document
    Then I follow "Nous contacter"
    Then I follow "Règlement"
    Then I press "Supprimer le document"
    Then I should see "Le document a bien été supprimé"

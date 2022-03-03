Feature: Gestion des écoles
  Je suis connecté
  J' ajoute l'école "Springfield"
  J' édite l'école
  Je supprime l'école de Waha
  Je ne peux pas supprimer l'école de Aye
  Enfants de l'école

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/ecole/"
    Then I should see "Liste des écoles"

  Scenario: Ajout une école
    Then I follow "Ajouter une école"
    And I fill in "ecole[nom]" with "Springfield"
    And I fill in "ecole[rue]" with "Rue des Armoiries"
    And I fill in "ecole[code_postal]" with "6900"
    And I fill in "ecole[localite]" with "Hargimont"
    And I press "Sauvegarder"
    Then I should see "Springfield"
    Then I should see "Rue des Armoiries"

  Scenario: Modifier une école
    Then I follow "Aye"
    Then I follow "Modifier"
    And I fill in "ecole[telephone]" with "084 55 66 99"
    And I press "Sauvegarder"
    Then I should see "084 55 66 99"

  Scenario: Supprimer une école
    Then I follow "Waha"
    Then I press "Supprimer l'école"
   # Then print last response
    Then I should see "L'école a bien été supprimée"

  Scenario: Supprimer une école avec enfants
    Then I follow "Aye"
    Then I press "Supprimer l'école"
    Then I should see "L'école contient des enfants et ne peut être supprimée"

  Scenario: Enfants de l' école
    Then I follow "Aye"
    Then I should see "Nelson"
    Then I should see "Peeter"
    Then I should see "Merlin"

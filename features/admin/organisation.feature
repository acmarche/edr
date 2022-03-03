Feature: Gestion de l' organisation
  Je suis connecté
  J' édite l'organisation
  Je supprime l'organisation
  Et j' ajoute l'organisation "Le paradis des enfants"

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/organisation/"
    Then I should see "Espace enfance jeunesse"

  Scenario: Modifier une organisation
    Then I follow "Modifier"
    And I fill in "organisation[telephone]" with "084 55 66 99"
    And I press "Sauvegarder"
    Then I should see "084 55 66 99"

  Scenario: Supprimer et ajouter une organisation
    Then I press "Supprimer l'organisation"
    #Then print last response
    Then I should see "L'organisation a bien été supprimée"
    #J'ajoute
    Then I follow "Ajouter une organisation"
    And I fill in "organisation[nom]" with "Le paradis des enfants"
    And I fill in "organisation[rue]" with "Rue des Armoiries"
    And I fill in "organisation[code_postal]" with "6900"
    And I fill in "organisation[localite]" with "Hargimont"
    And I fill in "organisation[email]" with "paradis@marche.be"
    And I press "Sauvegarder"
    Then I should see "Le paradis des enfants"
    Then I should see "Rue des Armoiries"


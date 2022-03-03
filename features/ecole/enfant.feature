Feature: Gestion des enfants

  Background:
    Given I am login with user "joseph@marche.be" and password "homer"
    Given I am on "/ecole/enfant/"

  Scenario: Je vois la liste des enfants
    Then I should see "Liste des enfants"
    Then I should not see "FERNANDEL Yves"
    Then I should not see "BOLT Jason"
    Then I should see "GAUTHIE Peeter"
    Then I should see "PERET Merlin"

  Scenario: Je consulte la fiche santé
    Then I should see "Liste des enfants"
    Then I follow "PERET Merlin"
    Then I follow "Fiche santé"
    And the response status code should be 200

Feature: Gestion des écoles
  Je suis connecté
  Je vois les enfants d'une école sur la page de détail

  Background:
    Given I am login with user "joseph@marche.be" and password "homer"
    Given I am on "/ecole/ecole"

  Scenario: Je vois les enfants d'une école sur la page de détail
    Then I should see "Liste de vos écoles"
    Then I should see "Aye"
    Then I should not see "Champlon"
    Then I follow "Aye"
    Then I should not see "Nelson"
    Then I should see "Peeter"
    Then I should see "Merlin"

Feature: Gestion de sa fiche animateur
  Je suis connecté
  Je modifie mes données

  Background:
    Given I am login with user "kevin@marche.be" and password "homer"
    Given I am on "/animateur/animateur"

  Scenario:  Je modifie mes données
    Then I should see "SZYSLAK Moe"
    Then I follow "Modifier"
    And I fill in "animateur[gsm]" with "0476 22 66 99"
    And I select "Masculin" from "animateur_sexe"
    And I press "Sauvegarder"
    Then I should see "0476 22 66 99"

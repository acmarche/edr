Feature: Gestion des utilisateurs
  Je suis connecté
  J'associe un parent sans les bons droits
  J'associe un parent avec les bons droits
  J'associe une école sans les bons droits
  J'associe une école avec les bons droits
  J'associe un animateur sans les bons droits
  J'associe un animateur avec les bons droits

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/user/"
    Then I should see "Liste des utilisateurs"

  Scenario: J'associe un parent sans les bons droits
    When I follow "Cohen Leonard"
    Then I follow "Associer un parent"
    Then I should see "Le compte n'a pas le rôle de parent"

  Scenario: J'associe un parent avec les bons droits
    When I follow "Cohen Albert"
    Then I follow "Associer un parent"
    And I select "GASPARD Aurore" from "associate_tuteur_tuteur"
    And I press "Sauvegarder"
    #Then print last response
    Then I should see "L'utilisateur a bien été associé."
    Then I should see "Un mail de bienvenue a été envoyé"
    Then I should see "GASPARD Aurore"

  Scenario: J'associe une école sans les bons droits
    When I follow "Cohen Albert"
    Then I follow "Associer une école"
    Then I should see "Le compte n'a pas le rôle de école"

  Scenario: J'associe une école avec les bons droits
    When I follow "Jacob Joseph"
    Then I follow "Associer une école"
    And I check "Aye"
    And I check "Waha"
    And I press "Sauvegarder"
    #Then print last response
    Then I should see "L'utilisateur a bien été associé."
    Then I should see "Aye"
    Then I should see "Waha"

Scenario: J'associe un animateur sans les bons droits
    When I follow "Cohen Leonard"
    Then I follow "Associer un animateur"
    Then I should see "Le compte n'a pas le rôle de animateur"

  Scenario: J'associe un animateur avec les bons droits
    When I follow "Malice Kevin"
    Then I follow "Associer un animateur"
    And I select "GUMBLE Barney" from "associate_animateur_animateur"
    And I press "Sauvegarder"
    #Then print last response
    Then I should see "L'utilisateur a bien été associé."
    Then I should see "Un mail de bienvenue a été envoyé"
    Then I should see "GUMBLE Barney"

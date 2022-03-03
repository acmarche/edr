Feature: Gestion des messages
  Je suis connecté
  J'envoie un message depuis un jour
  J'envoie un message depuis un groupe

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/presence/"
    Then I should see "Liste des présences"
    Then I select "Jeudi 19 septembre 2024" from "search_presence[jour]"
    And I press "Rechercher"

  Scenario: Message par jour
    Then I follow "new_message_from_jour"
    Then the response status code should be 200

  Scenario: Message par groupe
    Then I follow "new_message_from_groupe_Moyens"
    Then the response status code should be 200

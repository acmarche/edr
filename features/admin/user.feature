Feature: Gestion des utilisateurs
  Je suis connecté
  Ajout un utilisateur administrateur
  Ajout d'un utilisateur via le tuteur
  Je modifie un utilisateur
  Je modifie les rôles d'un utilisateur
  Je change le mot de passe et je me connecte avec le nouveau mot de passe
  Je supprime un utilisateur
  Je recherche l'utilisateur jf qui est admin

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/user/"
    Then I should see "Liste des utilisateurs"

  Scenario: Ajout un utilisateur administrateur
    When I follow "Ajouter un utilisateur"
    And I fill in "user[nom]" with "Botteman"
    And I fill in "user[prenom]" with "Bob"
    And I fill in "user[email]" with "bob@mail.com"
    And I fill in "user[plainPassword]" with "homer123"
    And I check "Administrateur"
    And I press "Sauvegarder"
    Then I should see "bob@mail.com"
    Then I should see "Botteman"
    Then I should see "ROLE_MERCREDI_ADMIN"

  Scenario: Ajout un utilisateur tuteur
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Peret"
    And I press "Rechercher"
    Then I follow "Peret"
    Then I follow "Créer un compte"
    And I fill in "user[plainPassword]" with "homer123"
    And I press "Sauvegarder"
    Then I should see "L'utilisateur a bien été ajouté"
    Then I should see "Dissocier"
    Then I should see "Parent"

  Scenario: Modifier un utilisateur
    When I follow "Cohen Leonard"
    Then I follow "Modifier"
    And I fill in "user_edit[nom]" with "De Vinci"
    And I press "Sauvegarder"
    Then I should see "DE VINCI Leonard"

  Scenario: Je modifie les rôles d'un utilisateur
    When I follow "Cohen Leonard"
    Then I follow "Rôles"
    And I check "Ecole"
    And I press "Sauvegarder"
    Then I should see "L'utilisateur a bien été modifié"
    Then I should see "ROLE_MERCREDI_ECOLE"

  Scenario: Je change le mot de passe et je me connecte avec le nouveau mot de passe
    When I follow "Cohen Albert"
    Then I follow "Changer le mot de passe"
    And I fill in "user_password[plainPassword]" with "lisa"
    And I press "Sauvegarder"
    Then I should see "Le mot de passe a bien été modifié"
    Given I am on "/logout"
    When I am login with user "albert@marche.be" and password "lisa"
    Then I should see "SIMPSON Bart"
    Then I follow "Mes coordonnées"
    Then I should see "SIMPSON Homer"

  Scenario: Supprimer un utilisateur
    When I follow "Cohen Albert"
    Then I press "Supprimer l'utilisateur"
   # Then print last response
    Then I should see "L'utilisateur a bien été supprimé"

  Scenario: Je recherche l'utilisateur jf qui est admin
    When I fill in "user_search[nom]" with "Jf"
    And I select "Administrateur" from "user_search_role"
    And I press "Rechercher"
    Then I should see "Simpson Jf"
    Then I should not see "Cohen Albert"

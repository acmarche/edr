Feature: Gestion des pages
  Je suis connecté
  J' ajoute une page"
  J' édite une page
  Je supprime une page system
  Je supprime une page non system
  Je trie les pages

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/page/"
    Then I should see "Liste des pages"

  Scenario: Ajouter une page
    Then I follow "Ajouter une page"
    And I fill in "page[nom]" with "Page de bienvenue"
    And I fill in "page[content]" with "Coucou ma super page"
    And I press "Sauvegarder"
    Then I should see "Page de bienvenue"

  Scenario: Modifier une page
    Then I follow "Accueil"
    Then I follow "Modifier"
    And I fill in "page[nom]" with "Accueil nouveau"
    And I press "Sauvegarder"
    Then I should see "Accueil nouveau"

  Scenario: Supprimer une page non system
    Then I follow "Page exemple"
    Then I press "Supprimer la page"
    Then I should see "La page a bien été supprimée"

  Scenario: Je trie les pages
    Then I follow "Ordre d'affichage"
    Then I should see "Ordre d'affichage des pages"

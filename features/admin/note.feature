Feature: Gestion des notes pour les enfants
  Ajouter un note
  Je modifie une note
  Je supprime une note

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"

  Scenario: Ajouter un note
    Then I follow "Ajouter une note"
    Then I should see "Nouvelle note pour PERET Merlin"
    And I fill in "note[remarque]" with "Ne pas oublier de faire une facture"
    And I press "Sauvegarder"
    Then I should see "La note a bien été ajoutée"
    Then I should see "Ne pas oublier de faire une facture"

  Scenario: Je modifie une note
    Then I follow "enfant_notes"
    Then I follow "A oublié son sac de gym"
    Then I follow "Modifier"
    And I fill in "note[remarque]" with "A oublié son sac de natation"
    And I press "Sauvegarder"
    Then I should see "La note a bien été modifiée"
    Then I should see "A oublié son sac de natation"

  Scenario: Je supprime une note
    Then I follow "enfant_notes"
    Then I follow "A oublié son sac de gym"
    Then I press "Supprimer la note"
    Then I should see "La note a bien été supprimée"

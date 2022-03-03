Feature: Gestion des questions
  Je suis connecté en admin
  J' ajoute une question avec un besoin de complément
  J' ajoute une question sans complément
  Je modifie une question
  Je supprime une queston
  Je supprime une queston qui a déjà une réponse
  Je trie les questions

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/sante/question"
    Then I should see "Liste des questions"

  Scenario: Ajout d'une question avec complément
    Then I follow "Ajouter une question"
    And I fill in "sante_question[nom]" with "Souffre-t-il d'autres allergies ?"
    And I fill in "sante_question[complement_label]" with "Précisez lesquelles"
    And I check "sante_question[complement]"
    And I press "Sauvegarder"
    Then I should see "La question a bien été ajoutée"
    Then I should see "Oui"
    Then I should see "Précisez lesquelles"

  Scenario: Ajout d'une question sans complément
    Then I follow "Ajouter une question"
    And I fill in "sante_question[nom]" with "Sait-il nager?"
    And I press "Sauvegarder"
    Then I should see "La question a bien été ajoutée"
    Then I should see "Non"

  Scenario: Supprimer une question
    Then I follow "Est-il somnambule?"
    Then I press "Supprimer la question"
    Then I should see "La question a bien été supprimée"

  Scenario: Supprimer une question qui a une réponse
    Then I follow "Porte-t-il des lunettes ?"
    Then I press "Supprimer la question"
    #Then print last response
    Then I should see "La question a bien été supprimée"

  Scenario: Je trie les questions
    Then I follow "Ordre d'affichage"
    #Then print last response
    Then I should see "Tri des questions"

Feature: Test de la gestion des accueils
  J' ajoute un accueil pour un enfant qui n'a pas de fiche santé
  J' ajoute un accueil pour un enfant qui a un fiche santé
  Je ne peux pas supprimer un accueil passée

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"

  Scenario: Je visionne un accueil
    Given I am on "/parent/enfant/"
    Then I follow "SIMPSON Bart"
    Then I should see "jeudi 9 juillet 2020"
    Then I follow "jeudi 9 juillet 2020"
    Then I should see "Accueil de SIMPSON Bart le jeudi 9 juillet 2020"
    And I should see "0,50 €"

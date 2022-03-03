Feature: Gestion des acceuils
  Ajouter un acceuil avec un tuteur
  Ajouter un acceuil avec 2 tuteurs
  Modifier un acceuil
  J'édite un acceuil déjà facturée

  Background:
    Given I am login with user "champlon@marche.be" and password "homer"
    Given I am on "/ecole/enfant/"
    Then I should see "Liste des enfants"

  Scenario: Ajout un acceuil avec tuteur
    Then I fill in "search_enfant_ecole[nom]" with "bart"
    And I press "Rechercher"
    Then I follow "SIMPSON Bart"
    Then I follow "Ajouter un accueil"
    Then I should see "Nouvel accueil pour SIMPSON Bart"
    And I fill in "accueil[date_jour]" with "2020-07-11"
    And I fill in "accueil[duree]" with "2"
    And I select "Matin" from "accueil_heure"
    And I press "Sauvegarder"
    Then I should see "L'acceuil a bien été ajouté"
    Then I should see "Matin"
    Then I should see "samedi 11 juillet 2020"

  Scenario: Ajout un acceuil 2 tuteurs
    Then I fill in "search_enfant_ecole[nom]" with "Fernandel"
    And I press "Rechercher"
    Then I should see "Fernandel"
    Then I follow "FERNANDEL Yves"
    Then I follow "Sous la garde de GASPARD Aurore"
    Then I should see "Nouvel accueil pour FERNANDEL Yves"
    And I fill in "accueil[date_jour]" with "2020-07-09"
    And I fill in "accueil[duree]" with "3"
    And I select "Matin" from "accueil_heure"
    And I press "Sauvegarder"
    Then I should see "L'acceuil a bien été ajouté"
    Then I should see "Matin"
    Then I should see "jeudi 9 juillet 2020"

  Scenario: J'édite un acceuil
    Then I fill in "search_enfant_ecole[nom]" with "bart"
    And I press "Rechercher"
    Then I follow "SIMPSON Bart"
    Then I follow "jeudi 9 juillet 2020"
    Then I follow "Modifier"
    And I fill in "accueil[duree]" with "3"
    And I press "Sauvegarder"
    Then I should see "L'accueil a bien été modifié"

  Scenario: Enfant non inscrit aux accueils
    Then I fill in "search_enfant_ecole[nom]" with "Jason"
    And I press "Rechercher"
    Then I should not see "BOLT Jason"

Feature: Gestion des acceuils
  Ajouter un acceuil avec un tuteur
  Ajouter un acceuil sans tuteur
  Ajouter un acceuil avec 2 tuteurs
  Ajouter un acceuil a la même date mais pas au meme moment de la journée
  Ajouter un acceuil a la même date au meme moment de la journée
  Modifier un acceuil
  J'édite un acceuil déjà facturée
  Supprimer un acceuil

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"

  Scenario: Ajout un acceuil avec tuteur
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Ses accueils"
    Then I follow "Ajouter un accueil"
    Then I should see "Nouvel accueil pour PERET Merlin"
    And I fill in "accueil[date_jour]" with "2020-07-10"
    And I fill in "accueil[duree]" with "2"
    And I select "Matin" from "accueil_heure"
    And I press "Sauvegarder"
    Then I should see "L'acceuil a bien été ajouté"
    Then I should see "Matin"
    Then I should see "1,00 €"
    Then I should see "vendredi 10 juillet 2020"

  Scenario: Ajout un acceuil 2 tuteurs
    Then I fill in "search_enfant[nom]" with "Fernandel"
    And I press "Rechercher"
    Then I should see "Fernandel"
    Then I follow "Fernandel"
    Then I follow "Ses accueils"
    Then I follow "Sous la garde de GASPARD Aurore"
    Then I should see "Nouvel accueil pour FERNANDEL Yves"
    And I fill in "accueil[date_jour]" with "2020-07-09"
    And I fill in "accueil[duree]" with "3"
    And I select "Matin" from "accueil_heure"
    And I press "Sauvegarder"
    Then I should see "L'acceuil a bien été ajouté"
    Then I should see "Matin"
    Then I should see "1,50 €"
    Then I should see "jeudi 9 juillet 2020"

  Scenario: Ajouter un acceuil a la même date mais pas au meme moment de la journée
    Then I fill in "search_enfant[nom]" with "Bart"
    And I press "Rechercher"
    Then I should see "Simpson"
    Then I follow "Simpson"
    Then I follow "Ses accueils"
    Then I follow "Ajouter un accueil"
    Then I should see "Nouvel accueil pour SIMPSON Bart"
    And I fill in "accueil[date_jour]" with "2020-12-09"
    And I fill in "accueil[duree]" with "2"
    And I select "Soir" from "accueil_heure"
    And I press "Sauvegarder"
    Then I should see "L'acceuil a bien été ajouté"
    Then I should see "Soir"

  Scenario: Ajouter un acceuil a la même date au meme moment de la journée
    Then I fill in "search_enfant[nom]" with "Bart"
    And I press "Rechercher"
    Then I should see "Simpson"
    Then I follow "Simpson"
    Then I follow "Ses accueils"
    Then I follow "Ajouter un accueil"
    Then I should see "Nouvel accueil pour SIMPSON Bart"
    And I fill in "accueil[date_jour]" with "2020-12-09"
    And I fill in "accueil[duree]" with "2"
    And I select "Matin" from "accueil_heure"
    And I press "Sauvegarder"
    Then I should see "L'enfant est déjà inscrit à cette date"
    Then I should not see "L'acceuil a bien été ajouté"

  Scenario: J'édite un acceuil
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Ses accueils"
    Then I follow "jeudi 9 juillet 2020"
    Then I should see "Accueil de PERET Merlin le jeudi 9 juillet 2020"
    Then I follow "Modifier"
    And I fill in "accueil[duree]" with "3"
    And I press "Sauvegarder"
    Then I should see "L'accueil a bien été modifié"
    Then I should see "1,50 €"

  Scenario: Je supprime un acceuil
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Ses accueils"
    Then I follow "jeudi 9 juillet 2020"
    Then I should see "Accueil de PERET Merlin le jeudi 9 juillet 2020"
    Then I press "Supprimer l' accueil"
    Then I should see "L'acceuil a bien été supprimé"

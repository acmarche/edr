Feature: Gestion des présences
  Ajouter une présence avec un tuteur
  Ajouter une présence sans tuteur
  Ajouter une présence avec 2 tuteurs
  Modifier une présence
  J'édite une présence déjà facturée
  Supprimer une présence

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"

  Scenario: Ajout une présence avec tuteur
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Ajouter une présence"
    Then I should see "Nouvelle présence pour PERET Merlin"
    And I select "Edr 4 septembre 2024" from "presence_new_jours"
    And I additionally select "Mardi 20 août 2024 (Pédagogique Aye,Champlon)" from "presence_new_jours"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été ajoutée"
    Then I should see "Edr 4 septembre 2024"
    Then I should see "Mardi 20 août 2024 (P)"

  Scenario: Ajout une présence 2 tuteurs
    Then I fill in "search_enfant[nom]" with "Fernandel"
    And I press "Rechercher"
    Then I should see "Fernandel"
    Then I follow "Fernandel"
    Then I follow "Sous la garde de GASPARD Aurore"
    Then I should see "Nouvelle présence pour FERNANDEL Yves"
    When I select "Edr 4 septembre 2024" from "presence_new_jours"
    When I additionally select "Edr 16 octobre 2024" from "presence_new_jours"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été ajoutée"
    Then I should see "Edr 4 septembre 2024"
    Then I should see "Edr 16 octobre 2024"

  Scenario: J'édite une présence
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Jeudi 19 septembre 2024"
    Then I should see "Détail de la présence de PERET Merlin"
    Then I follow "Modifier"
    And I select "Oui avec certificat" from "presence_absent"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été modifiée"
    Then I should see "Oui avec certificat"

  Scenario: J'édite une présence déjà facturée
    Then I fill in "search_enfant[nom]" with "Bart"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Edr 6 mai 2020"
    Then I should see "Détail de la présence de SIMPSON Bart du edr 6 mai 2020"
    Then I follow "Modifier"
    Then I should see "Une présence déjà facturée ne peut être éditée"

  Scenario: Je supprime une présence
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Jeudi 19 septembre 2024"
    Then I should see "Détail de la présence de PERET Merlin"
    Then I press "Supprimer la présence"
    Then I should see "La présence a bien été supprimée"

Feature: Test de la gestion des présences
  J' ajoute une présence pour un enfant qui n'a pas de fiche santé
  J' ajoute une présence pour un enfant qui a une fiche santé
  Je ne peux pas supprimer une présence passée

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"

  Scenario: Je vois les presences
    Then I follow "SIMPSON Bart"
    Then I should see "Edr 6 mai 2020"
    Then I should see "jeudi 9 juillet 2020"
    Then I should see "vendredi 10 juillet 2020"
    Then I should see "lundi 21 septembre 2020"
    Then I should see "Edr 6 mars 2024"
    Then I should not see "Lundi 16 décembre 2024"

  Scenario: J' ajoute une présence pour un enfant qui n'a pas de fiche santé
    When I follow "Inscription Mer-rec/Péda"
    Then I follow "SIMPSON Lisa"
    Then I should see "La fiche santé de votre enfant doit être complétée"

  Scenario: J' ajoute une présence pour un enfant qui a une fiche santé
    When I follow "Inscription Mer-rec/Péda"
    Then I follow "SIMPSON Bart"
    Then I should see "Sélectionnez des jours d'accueil"
    When I select day plus "16" from "presence_new_for_parent_jours"
    When I additionally select day plus "20" from "presence_new_for_parent_jours"
    And I additionally select "Mardi 20 août 2024 (Pédagogique Aye,Champlon)" from "presence_new_for_parent_jours"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été ajoutée"

  Scenario: Je ne peux pas supprimer une présence passée
    Then I follow "SIMPSON Bart"
    Then I should see "Edr 6 mai 2020"
    Then I follow "Edr 6 mai 2020"
    Then I should see "Détail de la présence de SIMPSON Bart du edr 6 mai 2020"
    Then I press "Supprimer la présence"
    Then I should see "Une présence passée ne peut être supprimée"

  Scenario: Je ne peux pas supprimer une présence facturée
    Then I follow "SIMPSON Bart"
    Then I should see "Edr 6 mai 2020"
    Then I follow "Edr 6 mai 2020"
    Then I should see "Détail de la présence de SIMPSON Bart du edr 6 mai 2020"
    Then I should see "Facture"
    Then I press "Supprimer la présence"
    Then I should see "Une présence passée ne peut être supprimée"

  Scenario: Je supprime une présence
    Then I follow "SIMPSON Bart"
    Then I follow "Edr 6 mars 2024"
    Then I press "Supprimer la présence"
    Then I should see "La présence a bien été supprimée"

Feature: Gestion des dates d'accueil
  Je suis connecté
  J' ajoute une journée non pédagogique"
  J' ajoute une journée pédagogique"
  Modifier le tarif d'un jour non pédagogique
  Modifier le tarif d'un jour pédagogique
  J' édite une date
  Je supprime une date sans presence
  Je supprime une date avec presence

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/jour/"
    Then I should see "Liste des jours d'accueil"

  Scenario: Ajouter une jour d'accueil non pédagogique
    Then I follow "Ajouter une date"
    And I fill in "jour[date_jour]" with "2020-05-02"
    And I press "Sauvegarder"
    Then I should see "Tarifs pour la journée du 02-05-2020"
    And I fill in "jour_tarification_degressive_with_forfait[prix1]" with "4"
    And I fill in "jour_tarification_degressive_with_forfait[prix2]" with "3"
    And I fill in "jour_tarification_degressive_with_forfait[prix3]" with "2"
    And I fill in "jour_tarification_degressive_with_forfait[forfait]" with "1"
    And I press "Sauvegarder"
    Then I should see "4 €"
    Then I should see "Samedi 2 mai 2020"

  Scenario: Ajouter une jour d'accueil pédagogique
    Then I follow "Ajouter une date"
    And I fill in "jour[date_jour]" with "2020-05-09"
    And I check "Aye"
    And I check "Hollogne"
    And I check "Journée pédagoque"
    And I press "Sauvegarder"
    Then I should see "Tarifs pour la journée du 09-05-2020"
    And I fill in "jour_tarification_full_day[prix1]" with "4"
    And I fill in "jour_tarification_full_day[prix2]" with "3"
    And I press "Sauvegarder"
    Then I should see "4 €"
    Then I should see "Par journée complète"
    Then I should see "Journée pédagogique"
    Then I should see "Samedi 9 mai 2020"
    Then I should see "Aye"
    Then I should see "Hollogne"

  Scenario: Modifier le tarif d'un jour non pédagogique
    Then I follow "Edr 11 Septembre 2024"
    Then I follow "Tarifs"
    And I fill in "jour_tarification_degressive_with_forfait[prix1]" with "3.66"
    And I fill in "jour_tarification_degressive_with_forfait[forfait]" with "1.22"
    And I press "Sauvegarder"
    Then I should see "3.66 €"
    Then I should see "1.22 €"

  Scenario: Modifier le tarif d'un jour pédagogique
    Then I follow "Mardi 20 Août 2024"
    Then I follow "Tarifs"
    And I fill in "jour_tarification_full_day[prix1]" with "3.99"
    And I fill in "jour_tarification_full_day[prix2]" with "1.33"
    And I press "Sauvegarder"
    Then I should see "3.99 €"
    Then I should see "1.33 €"

  Scenario: Modifier la date d'un jour
    Then I follow "Edr 11 Septembre 2024"
    Then I follow "Modifier"
    And I fill in "jour[date_jour]" with "2024-09-13"
    And I press "Sauvegarder"
    Then I should see "Vendredi 13 septembre 2024"

  Scenario: Supprimer une date sans présence
    Then I follow "Jeudi 26 Septembre 2024"
    Then I press "Supprimer la date"
   # Then print last response
    Then I should see "La date a bien été supprimée"

  Scenario: Supprimer une date avec présence
    Then I follow "Edr 11 Septembre 2024"
    Then I press "Supprimer la date"
    Then I should see "La date a bien été supprimée"

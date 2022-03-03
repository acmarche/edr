Feature: Ajout rapide d'un tuteur et d'un enfant
  Ajour avec création de compte
  Ajour sans création de compte

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/parent_enfant/"
    Then I should see "Nouveau parent et enfant"

  Scenario: Ajout avec création de compte
    And I fill in "tuteur_enfant_quick[tuteur][nom]" with "Flanders"
    And I fill in "tuteur_enfant_quick[tuteur][prenom]" with "Ned"
    And I fill in "tuteur_enfant_quick[tuteur][rue]" with "Rue Springfield"
    And I fill in "tuteur_enfant_quick[tuteur][code_postal]" with "6900"
    And I fill in "tuteur_enfant_quick[tuteur][localite]" with "New York"
    And I fill in "tuteur_enfant_quick[tuteur][telephone]" with "047 58 99 66"
    And I fill in "tuteur_enfant_quick[tuteur][email]" with "ned@domain.be"
    And I fill in "tuteur_enfant_quick[enfant][nom]" with "Flanders"
    And I fill in "tuteur_enfant_quick[enfant][prenom]" with "Rod"
    And I select "6" from "tuteur_enfant_quick[enfant][birthday][day]"
    And I select "déc." from "tuteur_enfant_quick[enfant][birthday][month]"
    And I select "2015" from "tuteur_enfant_quick[enfant][birthday][year]"
    And I select "Waha" from "tuteur_enfant_quick[enfant][ecole]"
    And I select "3M" from "tuteur_enfant_quick[enfant][annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "Un compte a été créé pour le parent"
    Then I should see "La fiche parent FLANDERS Ned bien été ajoutée"
    Then I should see "La fiche enfant FLANDERS Rod a bien été ajoutée"
    Then I should see "Le compte FLANDERS Ned a été créé"
    Then I should see "Adresse mail pour se connecter: ned@domain.be"

 Scenario:   Ajour sans création de compte
   And I fill in "tuteur_enfant_quick[tuteur][nom]" with "Flanders"
    And I fill in "tuteur_enfant_quick[tuteur][prenom]" with "Ned"
    And I fill in "tuteur_enfant_quick[tuteur][rue]" with "Rue Springfield"
    And I fill in "tuteur_enfant_quick[tuteur][code_postal]" with "6900"
    And I fill in "tuteur_enfant_quick[tuteur][localite]" with "New York"
    And I fill in "tuteur_enfant_quick[tuteur][telephone]" with "047 58 99 66"
    And I fill in "tuteur_enfant_quick[enfant][nom]" with "Flanders"
    And I fill in "tuteur_enfant_quick[enfant][prenom]" with "Rod"
    And I select "6" from "tuteur_enfant_quick[enfant][birthday][day]"
    And I select "déc." from "tuteur_enfant_quick[enfant][birthday][month]"
    And I select "2015" from "tuteur_enfant_quick[enfant][birthday][year]"
    And I select "Waha" from "tuteur_enfant_quick[enfant][ecole]"
    And I select "3M" from "tuteur_enfant_quick[enfant][annee_scolaire]"
    And I press "Sauvegarder"
    Then I should not see "Un compte a été créé pour le parent"
    Then I should see "La fiche parent FLANDERS Ned bien été ajoutée"
    Then I should see "La fiche enfant FLANDERS Rod a bien été ajoutée"
    Then I should not see "Le compte FLANDERS Ned a été créé"
    Then I should not see "Adresse mail pour se connecter: ned@domain.be"

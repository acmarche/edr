Feature: Gestion des factures
  Je suis connecté
  Je consulte une facture

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent/tuteur/"
    Then I should see "SIMPSON Homer"

  Scenario: Je ne vois pas une facture non envoyee
    Then I follow "Mes factures"
    Then I should see "Liste de vos factures"
    Then I should not see "Mardi 6 Octobre 2020"

  Scenario: Je consulte une facture envoyee
    Given I am on "/logout/"
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I should see "Simpson"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "06-2020"
    Then I follow "06-2020"
    Then I follow "Envoyer par mail"
    And I fill in "facture_send[sujet]" with "Voici votre facture"
    And I fill in "facture_send[texte]" with "Payer sur le compte x"
    And I press "Envoyer la facture"
    Then I should see "La facture a bien été envoyée"
    Given I am on "/logout/"
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent/tuteur/"
    Then I should see "SIMPSON Homer"
    Then I follow "Mes factures"
    Then I should see "Liste de vos factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Détails"
    Then I should see "edr 6 mai 2020"
    Then I should see "edr 9 décembre 2020"

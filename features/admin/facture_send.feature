Feature: Gestion des factures
  Je suis connecté
  J'envoie une facture
  J'envoie des factures par mail
  J'envoie des factures par papier

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I should see "Simpson"

  Scenario: J'envoie une facture d'un tuteur
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "06-2020"
    Then I follow "06-2020"
    Then I follow "Envoyer par mail"
    And I fill in "facture_send[sujet]" with "Voici votre facture"
    And I fill in "facture_send[texte]" with "Payer sur le compte x"
    And I press "Envoyer la facture"
    Then I should see "La facture a bien été envoyée"

  Scenario: J'envoie des factures par mail
    Given I am on "/admin/facture/send/select/month"
    Then I should see "Envoie de factures"
    And I fill in "facture_select_send[mois]" with "06-2020"
    And I select "Mail" from "facture_select_send_mode"
    Then I press "Sélectionner"
    Then I should see "Envoie des 1 factures pour 06-2020"
    And I fill in "facture_send_all[sujet]" with "Voici votre facture"
    And I fill in "facture_send_all[texte]" with "Payer sur le compte x"
    And I press "Envoyer les factures"
    Then I should see "Création des pdfs pour 06-2020"
    Then I should see "Tous les pdf ont été créés."
    Then I follow "Cliquez ici pour envoyer les mails"
    Then I should see "Envoie des mails pour 06-2020"

  Scenario: J'envoie des factures par papier
    Given I am on "/admin/facture/send/select/month"
    Then I should see "Envoie de factures"
    And I fill in "facture_select_send[mois]" with "06-2020"
    And I select "Papier" from "facture_select_send_mode"
    Then I press "Sélectionner"
    Then I should see "Télécharger les factures papier pour le mois de 06-2020"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Télécharger le pdf"
    And the response status code should be 200

Feature: Gestion des factures
  Je suis connecté
  Je paye totalement
  Je paie partiellement

  Background:
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
    Then I should see "edr 6 mai 2020"

  Scenario: Je paye totalement
    Then I follow "Payer totalement"
    Then I should see "Payer la facture n°"
    Then I fill in "facture_payer[payeLe]" with "2021-10-04"
    And I press "Sauvegarder"
    Then I should see "Facture payée"

  Scenario: Je paie partiellement
    Then I follow "Ajouter un décompte"
    Then I should see "Nouveau décompte pour la facture du mardi 6 octobre 2020 de SIMPSON Homer"
    Then I fill in "facture_decompte[payeLe]" with "2021-10-04"
    Then I fill in "facture_decompte[montant]" with "6"
    And I press "Sauvegarder"
    Then I should see "Le décompte a bien été ajouté"
    Then I should see "6,00 €"

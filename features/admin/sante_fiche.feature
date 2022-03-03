Feature: Gestion des fiches santés
  Je suis connecté
  J' ajoute une fiche a un enfant
  J' édite une fiche
  J'exporte en pdf

  Background:
    Given I am logged in as an admin

  Scenario: Nouvelle fiche santé
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Fernandel"
    And I press "Rechercher"
    Then I should see "Fernandel"
    Then I follow "Fernandel"
    Then I follow "Fiche santé"
    Then I should see "Cette enfant n'a pas encore de fiche santé"
    And I fill in "sante_fiche[medecin_nom]" with "Ledoux"
    And I fill in "sante_fiche[medecin_telephone]" with "084 32 55 66"
    And I fill in "sante_fiche[personne_urgence]" with "Papa et maman"
    And I fill in "sante_fiche[questions][0][reponseTxt]" with "1"
    And I fill in "sante_fiche[questions][0][remarque]" with "12-06-21"
    And I fill in "sante_fiche[questions][1][reponseTxt]" with "0"
    And I fill in "sante_fiche[questions][2][reponseTxt]" with "0"
    And I fill in "sante_fiche[accompagnateurs][0]" with "Mamy 084 25 66 99"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    Then I should see "084 32 55 66"
    Then I should see "Papa et maman"
    Then I should see "Ledoux"

  Scenario: Modifier une fiche
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Fiche santé"
    Then I follow "Modifier"
    And I fill in "sante_fiche[medecin_nom]" with "Docteur maboulle"
    And I fill in "sante_fiche[questions][0][reponseTxt]" with "1"
    And I fill in "sante_fiche[questions][0][remarque]" with "12-06-21"
    And I fill in "sante_fiche[questions][1][reponseTxt]" with "0"
    And I fill in "sante_fiche[questions][2][reponseTxt]" with "0"
    And I fill in "sante_fiche[accompagnateurs][0]" with "Mamy 084 25 66 99"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    Then I should see "Mamy 084 25 66 99"
    Then I should see "Docteur maboulle"

  Scenario: J'exporte une fiche en pdf
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Bart"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Fiche santé"
    Then I follow "Pdf"
    And the response status code should be 200

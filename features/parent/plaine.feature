Feature: Test pour les présences des plaines

  Je teste si plaine ouverte
  J' inscris pour un enfant qui n'a pas de fiche santé
  J' inscris pour un enfant qui a une fiche santé et est déjà inscrit
  J' inscris pour un enfant qui a une fiche santé et est pas inscrit

  Scenario: Je teste si plaine ouverte
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"
    Then I should not see "ouverte aux inscriptions"
    Given I am on "/logout"
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"
    Then I follow "Plaine de noel"
    Then I follow "Ouvrir les inscriptions"
    And I check "Ouvrir les inscriptions"
    And I press "Sauvegarder"
    Then I should see "Les inscriptions sont ouvertes aux parents"
    Given I am on "/logout"
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"
    Then I should see "Plaine de noel est ouverte aux inscriptions"

  Scenario: J' inscris pour un enfant qui n'a pas de fiche santé
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"
    Then I follow "Plaine de noel"
    Then I follow "Ouvrir les inscriptions"
    When I check "Ouvrir les inscriptions"
    And I press "Sauvegarder"
    Given I am on "/logout"
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"
    Then I follow "Plaine de noel est ouverte aux inscriptions"
    Then I follow "Inscrire mes enfants"
    And I check "SIMPSON Lisa"
    And I press "Inscrire"
    Then I should see "La fiche santé de SIMPSON Lisa doit être complétée"

  Scenario: J' inscris pour un enfant qui a une fiche santé
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"
    Then I follow "Plaine de noel"
    Then I follow "Ouvrir les inscriptions"
    And I check "Ouvrir les inscriptions"
    And I press "Sauvegarder"
    Given I am on "/logout"
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"
    Then I follow "Plaine de noel est ouverte aux inscriptions"
    Then I follow "Inscrire mes enfants"
    And I check "SIMPSON Bart"
    And I press "Inscrire"
    Then I should see "SIMPSON Bart a bien été inscrits à la plaine"
    Then I should see "Vous avez inscrit SIMPSON Bart"
    Then I press "Obtenir ma facture"
    Then I should see "La facture a bien été générée et envoyée sur votre mail"
    Given I am on "/parent/enfant/"
    Then I should see "Liste de mes enfants"
    Then I follow "SIMPSON Bart"
    Then I should see "Plaine de noel"

  Scenario: J' inscris pour un enfant qui a une fiche santé
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"
    Then I follow "Plaine de Jésus"
    Then I follow "Ouvrir les inscriptions"
    And I check "Ouvrir les inscriptions"
    And I press "Sauvegarder"
    Given I am on "/logout"
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"
    Then I follow "Plaine de Jésus est ouverte aux inscriptions"
    Then I follow "Inscrire mes enfants"
    And I check "SIMPSON Bart"
    And I press "Inscrire"
    Then I should see "SIMPSON Bart a bien été inscrits à la plaine"
    Then I should see "Vous avez inscrit SIMPSON Bart"
    Then I press "Obtenir ma facture"
    Then I should see "La facture a bien été générée et envoyée sur votre mail"
    Given I am on "/parent/enfant/"
    Then I should see "Liste de mes enfants"
    Then I follow "SIMPSON Bart"
    Then I should see "Plaine de Jésus"

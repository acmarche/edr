Feature: Gestion des présences des plaines
  Je suis connecté
  Ajout une présence avec plusieurs tuteurs
  Ajout une présence avec un tuteur

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"

  Scenario: Ajout une présence avec plusieurs tuteurs
    Then I follow "Plaine de noel"
    And I follow "Inscrire un enfant"
    And I should see "Nouvelle inscription à Plaine de noel"
    And I follow "Fernandel"
    And I follow "FERNANDEL Franz"
    Then I should see "Détails de l'inscription de FERNANDEL Yves à Plaine de noel"
    Then I should see "lundi 16 décembre 2024"
    Then I should see "mardi 17 décembre 2024"
    Then I should see "edr 18 décembre 2024"

  Scenario: Ajout une présence avec un tuteur
    Then I follow "Plaine de noel"
    And I follow "Inscrire un enfant"
    And I should see "Nouvelle inscription à Plaine de noel"
    And I follow "Peret"
    Then I should see "Détails de l'inscription de PERET Merlin à Plaine de noel"
    Then I should see "lundi 16 décembre 2024"
    Then I should see "mardi 17 décembre 2024"
    Then I should see "edr 18 décembre 2024"

  Scenario: Changer les dates de présence
    Then I follow "Plaine de noel"
    And I follow "Détails"
    And I follow "Changer ses dates"
    And I uncheck "17-12-2024"
    Then I press "Sauvegarder"
    And I should see "Les présences ont bien été modifiées"
    Then I should see "lundi 16 décembre 2024"
    Then I should not see "mardi 17 décembre 2024"
    Then I should see "edr 18 décembre 2024"

Feature: Test des pages parents
  Je suis sur la page d'accueil
  Je suis sur la page contact et j'envoie le formulaire

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent/tuteur/"
    Then I should see "SIMPSON Homer"

  Scenario: Je modifie les coordonnées
    Then I follow "Modifier"
    Then I should see "Modification de mes coordonnées"
    And I fill in "tuteur[rue]" with "Rue des Dentelles"
    And I fill in "tuteur[code_postal]" with "6900"
    And I fill in "tuteur[localite]" with "Marloie"
    And I fill in "tuteur[telephone]" with "047 58 99 66"
    And I fill in "tuteur[conjoint][nom_conjoint]" with "Mitch"
    And I fill in "tuteur[conjoint][prenom_conjoint]" with "Madeleine"
    And I press "Sauvegarder"
    Then I should see "047 58 99 66"
    Then I should see "Rue des Dentelles"
    Then I should see "Madeleine"

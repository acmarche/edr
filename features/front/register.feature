Feature: M'enregistrer
  Je m'inscris

  Background:
    Given I am on "/login"
    Then I should see "Authentifiez-vous"

  Scenario: Je m'enregistre
    Then I follow "M'enregistrer"
    Then I should see "M'enregistrer en tant que parent"
    And I fill in "registration_form[nom]" with "Burn"
    And I fill in "registration_form[prenom]" with "Charles"
    And I fill in "registration_form[email]" with "burn@hotmail.com"
    And I fill in "registration_form[telephone]" with "0476 22 55 88"
    And I fill in "registration_form[plainPassword]" with "Montgomery"
    And I check "Lire les conditions d'utilisation"
    And I press "M'enregistrer"
    And the response status code should be 200
    #Then print last response
    Then I should see "Votre compte a bien été créé, consultez votre boite mail"

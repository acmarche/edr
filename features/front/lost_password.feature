Feature: M'enregistrer
  Je m'inscris

  Background:
    Given I am on "/login"
    Then I should see "Authentifiez-vous"

  Scenario: J'ai perdu mon mot de passe'
    Then I follow "Mot de passe perdu?"
    Then I should see "Rappel du mot de passe"
    And I fill in "reset_password_request_form[email]" with "jf@marche.be"
    And I press "Envoyer"
    Then I should see "Un email vous a été envoyé, il contient un lien qui vous permettra de changer votre mot de passe."

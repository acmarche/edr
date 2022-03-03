Feature: Test des pages front
  Je suis sur la page d'accueil
  Je suis sur la page contact et j'envoie le formulaire

  Background:
    Given I am on "/"
    Then I should see "Bienvenue"

  Scenario: Test d'une page
    Then I follow "Modalités pratiques"
    Then I should see "Les prix et autres infos"

  Scenario: Page d'accueil
    Then I follow "Accueil"
    Then I should see "Bienvenue"

  Scenario: Je contact avec le formulaire
    Then I follow "Nous contacter"
    Then I should see "Plus de contacts"
    And I fill in "contact[nom]" with "Jfs"
    And I fill in "contact[email]" with "jf@hotmail.com"
    And I fill in "contact[texte]" with "Je souhaite connaitre les horaires"
    And I press "Envoyer"
    Then I should see "Le message a bien été envoyé."


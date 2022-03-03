Feature: Test des pages parents
  Je suis sur la page d'accueil
  J'affiche un enfant
  J' ajoute un enfant
  Je modifie un enfant sans fiche santé
  Je modifie sa fiche santé correctement
  Je modifie sa fiche santé sans répondre a une obligation de complément d'informations
  Je modifie sa fiche santé sans coche oui ou non et met un texte dans remarque
  Lisa n'a pas sa fiche santé complète
  Bart a sa fiche santé complète
  J'affiche une attestation fiscale

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent/enfant/"
    Then I should see "Liste de mes enfants"

  Scenario: J'affiche un enfant
    Then I follow "SIMPSON Lisa"
    Then I should see "SIMPSON Lisa"

  Scenario: J' ajoute un enfant
    Then I follow "Ajouter un enfant"
    Then I should see "Nouvel enfant"
    And I fill in "enfant_edit_for_parent[nom]" with "Funes"
    And I fill in "enfant_edit_for_parent[prenom]" with "Jules"
    And I select "6" from "enfant_edit_for_parent[birthday][day]"
    And I select "déc." from "enfant_edit_for_parent[birthday][month]"
    And I select "2015" from "enfant_edit_for_parent[birthday][year]"
    And I select "Waha" from "enfant_edit_for_parent[ecole]"
    And I select "Masculin" from "enfant_edit_for_parent[sexe]"
    And I select "3M" from "enfant_edit_for_parent[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "FUNES Jules"
    Then I should see "Waha"

  Scenario: Je modifie un enfant sans fiche santé
    Then I follow "SIMPSON Lisa"
    Then I should see "SIMPSON Lisa"
    Then I follow "Fiche santé"
    And I fill in "sante_fiche_etape1[registre_national]" with "12346"
    And I fill in "sante_fiche_etape1[poids]" with "25"
    And I select "Waha" from "sante_fiche_etape1[ecole]"
    And I select "3M" from "sante_fiche_etape1[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "L'enfant a bien été modifié"
    And I fill in "sante_fiche_etape2[medecin_nom]" with "Ledoux"
    And I fill in "sante_fiche_etape2[medecin_telephone]" with "084 32 55 66"
    And I fill in "sante_fiche_etape2[personne_urgence]" with "Papa et maman"
    And I fill in "sante_fiche_etape2[accompagnateurs][0]" with "Mamy 084 25 66 99"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    And I fill in "sante_fiche_etape3[questions][0][reponseTxt]" with "1"
    And I fill in "sante_fiche_etape3[questions][0][remarque]" with "23-10-2022"
    And I fill in "sante_fiche_etape3[questions][1][reponseTxt]" with "0"
    And I fill in "sante_fiche_etape3[questions][2][reponseTxt]" with "0"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    Then I should see "Waha"
    Then I should see "3M"
    Then I should see "25 kg"
    Then I should see "12346"

  Scenario: Je modifie sa fiche santé sans répondre a une obligation de complément d'informations
    Then I follow "SIMPSON Lisa"
    Then I should see "SIMPSON Lisa"
    Then I follow "Fiche santé"
    And I fill in "sante_fiche_etape1[registre_national]" with "12346"
    And I fill in "sante_fiche_etape1[poids]" with "25"
    And I select "Waha" from "sante_fiche_etape1[ecole]"
    And I select "3M" from "sante_fiche_etape1[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "L'enfant a bien été modifié"
    And I fill in "sante_fiche_etape2[medecin_nom]" with "Ledoux"
    And I fill in "sante_fiche_etape2[medecin_telephone]" with "084 32 55 66"
    And I fill in "sante_fiche_etape2[personne_urgence]" with "Papa et maman"
    And I fill in "sante_fiche_etape2[accompagnateurs][0]" with "Mamy 084 25 66 99"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    And I fill in "sante_fiche_etape3[questions][0][reponseTxt]" with "1"
    And I fill in "sante_fiche_etape3[questions][1][reponseTxt]" with "0"
    And I fill in "sante_fiche_etape3[questions][2][reponseTxt]" with "0"
    And I press "Sauvegarder"
    Then I should not see "Le formulaire santé a bien été enregistré"
    Then I should see "A-t-il été vacciné contre le tétanos? : Indiquez => Date du dernier rappel"

  Scenario: Lisa n'a pas sa fiche santé complète
    Then I follow "SIMPSON Lisa"
    Then I should see "Attention la fiche santé n'est pas complète, veuillez la remplir."

  Scenario: Bart a sa fiche santé complète
    Then I follow "SIMPSON Bart"
    Then I should not see "Attention la fiche santé n'est pas complète, veuillez la remplir."

  #Scenario: J'affiche une attestation fiscale
    #Then I follow "Télécharger 2021"
    #And the response status code should be 200

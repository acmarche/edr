<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Drop tables of removed features (animateur, plaine, jour/presence, facture/creance/paiement,
 * reduction, message, note, document) and the unused user_ecole / user_animateur links.
 * All these tables were empty when the code was removed.
 */
final class Version20261124130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop tables of removed, unused features';
    }

    public function isTransactional(): bool
    {
        // MySQL/MariaDB DDL statements commit implicitly
        return false;
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE animateur_jour DROP FOREIGN KEY `FK_A80C121D220C6AD0`');
        $this->addSql('ALTER TABLE animateur_jour DROP FOREIGN KEY `FK_A80C121D7F05C301`');
        $this->addSql('ALTER TABLE creance DROP FOREIGN KEY `FK_82D1060E86EC68D8`');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY `FK_FE866410202EA973`');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY `FK_FE86641086EC68D8`');
        $this->addSql('ALTER TABLE facture_complement DROP FOREIGN KEY `FK_12BBF1307F2DEE08`');
        $this->addSql('ALTER TABLE facture_decompte DROP FOREIGN KEY `FK_84C6EF587F2DEE08`');
        $this->addSql('ALTER TABLE facture_presence DROP FOREIGN KEY `FK_7B88C9547F2DEE08`');
        $this->addSql('ALTER TABLE facture_presence DROP FOREIGN KEY `FK_7B88C954C03CB092`');
        $this->addSql('ALTER TABLE facture_reduction DROP FOREIGN KEY `FK_7B4F69EC7F2DEE08`');
        $this->addSql('ALTER TABLE jour DROP FOREIGN KEY `FK_DA17D9C5202EA973`');
        $this->addSql('ALTER TABLE jour_ecole DROP FOREIGN KEY `FK_B50CBDF220C6AD0`');
        $this->addSql('ALTER TABLE jour_ecole DROP FOREIGN KEY `FK_B50CBDF77EF1B1E`');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY `FK_CFBDFA14450D2529`');
        $this->addSql('ALTER TABLE page_document DROP FOREIGN KEY `FK_58F68D56C33F7837`');
        $this->addSql('ALTER TABLE page_document DROP FOREIGN KEY `FK_58F68D56C4663E4`');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY `FK_B1DC7A1E450D2529`');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY `FK_B1DC7A1E86EC68D8`');
        $this->addSql('ALTER TABLE plaine_groupe DROP FOREIGN KEY `FK_483C3DF8202EA973`');
        $this->addSql('ALTER TABLE plaine_groupe DROP FOREIGN KEY `FK_483C3DF8A904035D`');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY `FK_6977C7A5220C6AD0`');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY `FK_6977C7A52A4C4478`');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY `FK_6977C7A5450D2529`');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY `FK_6977C7A586EC68D8`');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY `FK_6977C7A5C03CB092`');
        $this->addSql('ALTER TABLE user_animateur DROP FOREIGN KEY `FK_208456C87F05C301`');
        $this->addSql('ALTER TABLE user_animateur DROP FOREIGN KEY `FK_208456C8A76ED395`');
        $this->addSql('ALTER TABLE user_ecole DROP FOREIGN KEY `FK_EBBA91F477EF1B1E`');
        $this->addSql('ALTER TABLE user_ecole DROP FOREIGN KEY `FK_EBBA91F4A76ED395`');
        $this->addSql('DROP TABLE animateur');
        $this->addSql('DROP TABLE animateur_jour');
        $this->addSql('DROP TABLE creance');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE facture_complement');
        $this->addSql('DROP TABLE facture_cron');
        $this->addSql('DROP TABLE facture_decompte');
        $this->addSql('DROP TABLE facture_presence');
        $this->addSql('DROP TABLE facture_reduction');
        $this->addSql('DROP TABLE jour');
        $this->addSql('DROP TABLE jour_ecole');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE page_document');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE plaine');
        $this->addSql('DROP TABLE plaine_groupe');
        $this->addSql('DROP TABLE presence');
        $this->addSql('DROP TABLE reduction');
        $this->addSql('DROP TABLE user_animateur');
        $this->addSql('DROP TABLE user_ecole');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE animateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, rue VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, code_postal VARCHAR(6) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, localite VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, sexe VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone_bureau VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, gsm VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, archived TINYINT NOT NULL, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE animateur_jour (animateur_id INT NOT NULL, jour_id INT NOT NULL, INDEX IDX_A80C121D220C6AD0 (jour_id), INDEX IDX_A80C121D7F05C301 (animateur_id), PRIMARY KEY (animateur_id, jour_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE creance (id INT AUTO_INCREMENT NOT NULL, tuteur_id INT NOT NULL, montant NUMERIC(4, 2) NOT NULL, date_le DATETIME DEFAULT NULL, paye_le DATETIME DEFAULT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_82D1060E86EC68D8 (tuteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, tuteur_id INT DEFAULT NULL, plaine_id INT DEFAULT NULL, facture_le DATETIME NOT NULL, paye_le DATETIME DEFAULT NULL, envoye_le DATETIME DEFAULT NULL, envoye_a VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, mois VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, plaine_nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ecoles VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, montant_obsolete NUMERIC(6, 2) DEFAULT NULL, cloture_obsolete TINYINT DEFAULT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, rue VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, code_postal VARCHAR(6) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, localite VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, communication VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_FE866410202EA973 (plaine_id), INDEX IDX_FE86641086EC68D8 (tuteur_id), UNIQUE INDEX UNIQ_FE866410F9AFB5EB (communication), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture_complement (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, forfait NUMERIC(4, 2) DEFAULT NULL, pourcentage NUMERIC(4, 2) DEFAULT NULL, date_le DATETIME NOT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_12BBF1307F2DEE08 (facture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture_cron (id INT AUTO_INCREMENT NOT NULL, done TINYINT NOT NULL, from_adresse VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, subject VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, month VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F1CB2C348EB61006 (month), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture_decompte (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, paye_le DATETIME DEFAULT NULL, montant NUMERIC(4, 2) NOT NULL, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_84C6EF587F2DEE08 (facture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture_presence (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, reduction_id INT DEFAULT NULL, heure VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, duree INT DEFAULT NULL, presence_date DATE NOT NULL, cout_brut NUMERIC(4, 2) NOT NULL, cout_calculated NUMERIC(4, 2) NOT NULL, presence_id INT NOT NULL, object_type VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, pedagogique TINYINT NOT NULL, ordre SMALLINT NOT NULL, absent SMALLINT NOT NULL COMMENT \'-1 sans certif, 1 avec certfi\', INDEX IDX_7B88C9547F2DEE08 (facture_id), INDEX IDX_7B88C954C03CB092 (reduction_id), UNIQUE INDEX UNIQ_7B88C954F328FFC411CB6B3A (presence_id, object_type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture_reduction (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, forfait NUMERIC(4, 2) DEFAULT NULL, pourcentage NUMERIC(4, 2) DEFAULT NULL, date_le DATETIME NOT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_7B4F69EC7F2DEE08 (facture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE jour (id INT AUTO_INCREMENT NOT NULL, plaine_id INT DEFAULT NULL, date_jour DATE NOT NULL, prix1 NUMERIC(4, 2) NOT NULL, prix2 NUMERIC(4, 2) NOT NULL, prix3 NUMERIC(4, 2) NOT NULL, color VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, archived TINYINT NOT NULL, pedagogique TINYINT NOT NULL, forfait NUMERIC(4, 2) NOT NULL, id_old INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DA17D9C5202EA973 (plaine_id), UNIQUE INDEX UNIQ_DA17D9C5FAC950315F899A95202EA973 (date_jour, pedagogique, plaine_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE jour_ecole (jour_id INT NOT NULL, ecole_id INT NOT NULL, INDEX IDX_B50CBDF220C6AD0 (jour_id), INDEX IDX_B50CBDF77EF1B1E (ecole_id), PRIMARY KEY (jour_id, ecole_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sujet LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, texte LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, destinataires JSON NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, enfant_id INT DEFAULT NULL, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, archived TINYINT NOT NULL, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CFBDFA14450D2529 (enfant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE page_document (page_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_58F68D56C33F7837 (document_id), INDEX IDX_58F68D56C4663E4 (page_id), PRIMARY KEY (page_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, tuteur_id INT DEFAULT NULL, enfant_id INT DEFAULT NULL, montant NUMERIC(6, 2) NOT NULL, date_paiement DATE NOT NULL, type_paiement VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, mode_paiement VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ordre SMALLINT DEFAULT 0 COMMENT \'1,2, suviant\', cloture TINYINT DEFAULT 0 NOT NULL, remarques LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_B1DC7A1E450D2529 (enfant_id), INDEX IDX_B1DC7A1E86EC68D8 (tuteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE plaine (id INT AUTO_INCREMENT NOT NULL, communication VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, inscription_open TINYINT NOT NULL, prix1 NUMERIC(4, 2) NOT NULL, prix2 NUMERIC(4, 2) NOT NULL, prix3 NUMERIC(4, 2) NOT NULL, prematernelle TINYINT DEFAULT NULL, archived TINYINT NOT NULL, id_old INT DEFAULT NULL, slug VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE plaine_groupe (id INT AUTO_INCREMENT NOT NULL, plaine_id INT NOT NULL, groupe_scolaire_id INT DEFAULT NULL, inscription_maximum INT NOT NULL, file_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_483C3DF8202EA973 (plaine_id), INDEX IDX_483C3DF8A904035D (groupe_scolaire_id), UNIQUE INDEX UNIQ_483C3DF8202EA973A904035D (plaine_id, groupe_scolaire_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE presence (id INT AUTO_INCREMENT NOT NULL, tuteur_id INT DEFAULT NULL, enfant_id INT DEFAULT NULL, jour_id INT DEFAULT NULL, reduction_id INT DEFAULT NULL, paiement_id INT DEFAULT NULL, absent SMALLINT NOT NULL COMMENT \'-1 sans certif, 1 avec certfi\', ordre SMALLINT NOT NULL, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, half TINYINT NOT NULL, confirmed TINYINT NOT NULL, id_old INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6977C7A5220C6AD0 (jour_id), INDEX IDX_6977C7A52A4C4478 (paiement_id), INDEX IDX_6977C7A5450D2529 (enfant_id), INDEX IDX_6977C7A586EC68D8 (tuteur_id), INDEX IDX_6977C7A5C03CB092 (reduction_id), UNIQUE INDEX UNIQ_6977C7A5220C6AD0450D2529 (jour_id, enfant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reduction (id INT AUTO_INCREMENT NOT NULL, pourcentage DOUBLE PRECISION DEFAULT NULL, forfait DOUBLE PRECISION DEFAULT NULL, nom VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_animateur (user_id INT NOT NULL, animateur_id INT NOT NULL, INDEX IDX_208456C87F05C301 (animateur_id), INDEX IDX_208456C8A76ED395 (user_id), PRIMARY KEY (user_id, animateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_ecole (user_id INT NOT NULL, ecole_id INT NOT NULL, INDEX IDX_EBBA91F477EF1B1E (ecole_id), INDEX IDX_EBBA91F4A76ED395 (user_id), PRIMARY KEY (user_id, ecole_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE animateur_jour ADD CONSTRAINT `FK_A80C121D220C6AD0` FOREIGN KEY (jour_id) REFERENCES jour (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animateur_jour ADD CONSTRAINT `FK_A80C121D7F05C301` FOREIGN KEY (animateur_id) REFERENCES animateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE creance ADD CONSTRAINT `FK_82D1060E86EC68D8` FOREIGN KEY (tuteur_id) REFERENCES tuteur (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT `FK_FE866410202EA973` FOREIGN KEY (plaine_id) REFERENCES plaine (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT `FK_FE86641086EC68D8` FOREIGN KEY (tuteur_id) REFERENCES tuteur (id)');
        $this->addSql('ALTER TABLE facture_complement ADD CONSTRAINT `FK_12BBF1307F2DEE08` FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE facture_decompte ADD CONSTRAINT `FK_84C6EF587F2DEE08` FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE facture_presence ADD CONSTRAINT `FK_7B88C9547F2DEE08` FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE facture_presence ADD CONSTRAINT `FK_7B88C954C03CB092` FOREIGN KEY (reduction_id) REFERENCES reduction (id)');
        $this->addSql('ALTER TABLE facture_reduction ADD CONSTRAINT `FK_7B4F69EC7F2DEE08` FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE jour ADD CONSTRAINT `FK_DA17D9C5202EA973` FOREIGN KEY (plaine_id) REFERENCES plaine (id)');
        $this->addSql('ALTER TABLE jour_ecole ADD CONSTRAINT `FK_B50CBDF220C6AD0` FOREIGN KEY (jour_id) REFERENCES jour (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jour_ecole ADD CONSTRAINT `FK_B50CBDF77EF1B1E` FOREIGN KEY (ecole_id) REFERENCES ecole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT `FK_CFBDFA14450D2529` FOREIGN KEY (enfant_id) REFERENCES enfant (id)');
        $this->addSql('ALTER TABLE page_document ADD CONSTRAINT `FK_58F68D56C33F7837` FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_document ADD CONSTRAINT `FK_58F68D56C4663E4` FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT `FK_B1DC7A1E450D2529` FOREIGN KEY (enfant_id) REFERENCES enfant (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT `FK_B1DC7A1E86EC68D8` FOREIGN KEY (tuteur_id) REFERENCES tuteur (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE plaine_groupe ADD CONSTRAINT `FK_483C3DF8202EA973` FOREIGN KEY (plaine_id) REFERENCES plaine (id)');
        $this->addSql('ALTER TABLE plaine_groupe ADD CONSTRAINT `FK_483C3DF8A904035D` FOREIGN KEY (groupe_scolaire_id) REFERENCES groupe_scolaire (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT `FK_6977C7A5220C6AD0` FOREIGN KEY (jour_id) REFERENCES jour (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT `FK_6977C7A52A4C4478` FOREIGN KEY (paiement_id) REFERENCES paiement (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT `FK_6977C7A5450D2529` FOREIGN KEY (enfant_id) REFERENCES enfant (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT `FK_6977C7A586EC68D8` FOREIGN KEY (tuteur_id) REFERENCES tuteur (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT `FK_6977C7A5C03CB092` FOREIGN KEY (reduction_id) REFERENCES reduction (id)');
        $this->addSql('ALTER TABLE user_animateur ADD CONSTRAINT `FK_208456C87F05C301` FOREIGN KEY (animateur_id) REFERENCES animateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_animateur ADD CONSTRAINT `FK_208456C8A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_ecole ADD CONSTRAINT `FK_EBBA91F477EF1B1E` FOREIGN KEY (ecole_id) REFERENCES ecole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_ecole ADD CONSTRAINT `FK_EBBA91F4A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}

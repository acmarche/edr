<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223081424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, roles FROM user'
        );

        foreach ($rows as $row) {
            $data = unserialize($row['roles']);

            $this->connection->update(
                'user',
                ['roles' => json_encode($data)],
                ['id' => $row['id']]
            );
        }
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSql('ALTER TABLE accueil DROP FOREIGN KEY FK_2994CBE450D2529');
            $this->addSql('ALTER TABLE accueil DROP FOREIGN KEY FK_2994CBE86EC68D8');
            $this->addSql('DROP TABLE accueil');
            $this->addSql('ALTER TABLE message CHANGE destinataires destinataires VARCHAR(255) NOT NULL');
            $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
            $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
            $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
            $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
            $this->addSql(
                'CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)'
            );
        }

        public
        function down(Schema $schema): void
        {
            // this down() migration is auto-generated, please modify it to your needs
            $this->addSql(
                'CREATE TABLE accueil (id INT AUTO_INCREMENT NOT NULL, tuteur_id INT NOT NULL, enfant_id INT NOT NULL, date_jour DATE NOT NULL, duree SMALLINT NOT NULL, heure VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, remarque LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, user_add VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, heure_retard DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, uuid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2994CBE450D2529 (enfant_id), INDEX IDX_2994CBE86EC68D8 (tuteur_id), UNIQUE INDEX UNIQ_2994CBEFAC95031450D25291173E8B8 (date_jour, enfant_id, heure), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
            );
            $this->addSql(
                'ALTER TABLE accueil ADD CONSTRAINT FK_2994CBE450D2529 FOREIGN KEY (enfant_id) REFERENCES enfant (id)'
            );
            $this->addSql(
                'ALTER TABLE accueil ADD CONSTRAINT FK_2994CBE86EC68D8 FOREIGN KEY (tuteur_id) REFERENCES tuteur (id)'
            );
            $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
            $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
            $this->addSql(
                'ALTER TABLE message CHANGE destinataires destinataires LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\''
            );
            $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        }
    }

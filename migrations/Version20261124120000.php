<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * message.destinataires: legacy "array" (PHP serialized) / VARCHAR(255) -> JSON list of emails.
 */
final class Version20261124120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert message.destinataires to a JSON list of email addresses';
    }

    public function isTransactional(): bool
    {
        // MySQL/MariaDB DDL statements commit implicitly
        return false;
    }

    public function up(Schema $schema): void
    {
        // Widen first so converted values never get truncated
        $this->connection->executeStatement('ALTER TABLE message MODIFY destinataires LONGTEXT NOT NULL');

        $rows = $this->connection->fetchAllAssociative('SELECT id, destinataires FROM message');
        foreach ($rows as $row) {
            $this->connection->update(
                'message',
                ['destinataires' => json_encode(self::toEmails((string) $row['destinataires']), \JSON_THROW_ON_ERROR)],
                ['id' => $row['id']],
            );
        }

        $this->addSql('ALTER TABLE message CHANGE destinataires destinataires JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // LONGTEXT instead of the former VARCHAR(255) so no recipient list is truncated
        $this->addSql('ALTER TABLE message CHANGE destinataires destinataires LONGTEXT NOT NULL');
    }

    /**
     * @return list<string>
     */
    private static function toEmails(string $value): array
    {
        $value = trim($value);
        if ('' === $value) {
            return [];
        }

        $json = json_decode($value, true);
        if (\is_array($json)) {
            return array_values(array_filter($json, is_string(...)));
        }

        $unserialized = @unserialize($value, ['allowed_classes' => false]);
        if (\is_array($unserialized)) {
            return array_values(array_filter($unserialized, is_string(...)));
        }

        // Fallback (e.g. serialized data truncated by the VARCHAR(255) column): salvage the addresses
        preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $value, $matches);

        return array_values(array_unique($matches[0]));
    }
}

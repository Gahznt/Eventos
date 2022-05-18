<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201319194226 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE anpad_events.user_association ADD COLUMN level TINYINT(2) NULL AFTER last_pay');
        $this->addSql('ALTER TABLE anpad_events.user_association ADD COLUMN status_pay TINYINT(2) NULL AFTER level');

    }

    public function down(Schema $schema) : void
    {
    }
}

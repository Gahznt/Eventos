<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201219194214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD COLUMN payment TINYINT(2) NULL AFTER extension, ADD COLUMN level TINYINT(2) NULL AFTER payment, ADD COLUMN last_pay DATE NULL AFTER level, ADD COLUMN updated_at DATE NULL AFTER last_pay');
    }

    public function down(Schema $schema) : void
    {
    }
}

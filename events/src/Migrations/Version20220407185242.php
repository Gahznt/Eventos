<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407185242 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subsection CHANGE description_portuguese description_portuguese TEXT DEFAULT NULL, CHANGE description_english description_english TEXT DEFAULT NULL, CHANGE description_spanish description_spanish TEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subsection CHANGE description_portuguese description_portuguese TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE description_english description_english TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE description_spanish description_spanish TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204208150668 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("RENAME TABLE `event_signup` TO `edition_signup`;");
        $this->addSql("RENAME TABLE `event_signup_articles` TO `edition_signup_articles`;");
        $this->addSql("ALTER TABLE `edition_signup_articles` CHANGE `event_signup_id` `edition_signup_id` BIGINT(20)  UNSIGNED  NOT NULL;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("RENAME TABLE `edition_signup` TO `event_signup`;");
        $this->addSql("RENAME TABLE `edition_signup_articles` TO `event_signup_articles`;");
        $this->addSql("ALTER TABLE `edition_signup_articles` CHANGE `edition_signup_id` `event_signup_id` BIGINT(20)  UNSIGNED  NOT NULL;");
    }
}

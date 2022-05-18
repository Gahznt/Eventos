<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211029182254 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certificate CHANGE variables variables LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql("INSERT INTO `theme_submission_config` (`id`, `created_at`, `updated_at`, `deleted_at`, `year`, `is_available`, `is_current`) VALUES (1, '2020-04-19 15:28:19', NULL, NULL, '2020', 0, 0)");
        $this->addSql("UPDATE `user_themes` SET `theme_submission_config` = 1");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

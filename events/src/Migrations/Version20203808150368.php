<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203808150357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_themes` ADD `user_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL  AFTER `id`");
        $this->addSql("ALTER TABLE `user_themes` ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_themes` DROP FOREIGN KEY `user_themes_user_id`");
        $this->addSql("ALTER TABLE `user_themes` DROP `user_id`");
        $this->addSql("ALTER TABLE `user_themes` DROP FOREIGN KEY `user_themes_theme_id`");
        $this->addSql("ALTER TABLE `user_themes` DROP `theme_id`");
    }
}

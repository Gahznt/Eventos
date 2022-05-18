<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209241400122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `user_articles` CHANGE `division_id` `division_id` INT(10)  UNSIGNED  NULL");
        $this->addSql("ALTER TABLE `user_articles` CHANGE `user_id` `user_id` BIGINT(20)  UNSIGNED  NULL");
        $this->addSql("ALTER TABLE `user_themes` CHANGE `division_id` `division_id` INT(10)  UNSIGNED  NULL");

        $this->addSql("ALTER TABLE `keyword` DROP FOREIGN KEY `keyword_ibfk_2`");

        $this->addSql("delete from keyword");

        $this->addSql("ALTER TABLE `keyword` CHANGE `theme_id` `theme_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `keyword` ADD FOREIGN KEY (`theme_id`) REFERENCES `user_themes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");

        $this->addSql("ALTER TABLE `user_themes_evaluation_log` CHANGE `user_id` `user_id` BIGINT(20)  UNSIGNED  NULL;");
        $this->addSql("ALTER TABLE `user_themes_evaluation_log` CHANGE `ip` `ip` TEXT  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL;");

    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203807150317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `panel` CHANGE `season_id` `edition_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `panel` RENAME INDEX `season_id` TO `edition_id`");
        $this->addSql("ALTER TABLE `theme` CHANGE `season_id` `edition_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `theme` RENAME INDEX `season_id` TO `edition_id`");
        $this->addSql("ALTER TABLE `user_articles` CHANGE `season_id` `edition_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `user_articles` RENAME INDEX `season_id` TO `edition_id`");
        $this->addSql("ALTER TABLE `user_themes` CHANGE `season_id` `edition_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `user_themes` RENAME INDEX `season_id` TO `edition_id`");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `panel` CHANGE `edition_id` `season_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `panel` RENAME INDEX `edition_id` TO `season_id`");
        $this->addSql("ALTER TABLE `theme` CHANGE `edition_id` `season_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `theme` RENAME INDEX `edition_id` TO `season_id`");
        $this->addSql("ALTER TABLE `user_articles` CHANGE `edition_id` `season_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `user_articles` RENAME INDEX `edition_id` TO `season_id`");
        $this->addSql("ALTER TABLE `user_themes` CHANGE `edition_id` `season_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `user_themes` RENAME INDEX `edition_id` TO `season_id`");
    }
}

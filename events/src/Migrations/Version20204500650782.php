<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650782 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Keywords: drop key, change type & rename
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP KEY `keyword_first_id`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `keyword_first_id` text NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` CHANGE `keyword_first_id` `keyword_one` text;");

        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP KEY `keyword_second_id`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `keyword_second_id` text NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` CHANGE `keyword_second_id` `keyword_two` text;");

        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP KEY `keyword_three_id`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `keyword_three_id` text NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` CHANGE `keyword_three_id` `keyword_three` text;");

        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP KEY `keyword_four_id`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `keyword_four_id` text NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` CHANGE `keyword_four_id` `keyword_four` text;");

        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP KEY `keyword_five_id`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `keyword_five_id` text NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` CHANGE `keyword_five_id` `keyword_five` text;");
        
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP KEY `keyword_six_id`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `keyword_six_id` text NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` CHANGE `keyword_six_id` `keyword_six` text;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

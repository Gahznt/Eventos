<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824173253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE system_evaluation_config ADD detailed_scheduling_available INT NOT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL, CHANGE edition_id edition_id INT UNSIGNED DEFAULT NULL, CHANGE article_submission_available article_submission_available INT NOT NULL, CHANGE evaluate_article_available evaluate_article_available INT NOT NULL, CHANGE results_available results_available INT NOT NULL, CHANGE article_free article_free INT NOT NULL, CHANGE automatic_certiticates automatic_certiticates INT NOT NULL, CHANGE free_certiticates free_certiticates INT NOT NULL, CHANGE ensalement_general ensalement_general INT NOT NULL, CHANGE ensalement_priority ensalement_priority INT NOT NULL, CHANGE free_sections free_sections INT NOT NULL, CHANGE free_signup free_signup INT NOT NULL, CHANGE panel_submission_available panel_submission_available INT NOT NULL, CHANGE thesis_submission_available thesis_submission_available INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE system_evaluation_config DROP detailed_scheduling_available, CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE user_id user_id BIGINT UNSIGNED NOT NULL, CHANGE edition_id edition_id INT UNSIGNED NOT NULL, CHANGE article_submission_available article_submission_available TINYINT(1) DEFAULT \'0\', CHANGE evaluate_article_available evaluate_article_available TINYINT(1) DEFAULT \'0\', CHANGE results_available results_available TINYINT(1) DEFAULT \'0\', CHANGE article_free article_free TINYINT(1) DEFAULT \'0\', CHANGE automatic_certiticates automatic_certiticates TINYINT(1) DEFAULT \'0\', CHANGE free_certiticates free_certiticates TINYINT(1) DEFAULT NULL, CHANGE ensalement_general ensalement_general TINYINT(1) DEFAULT \'0\', CHANGE ensalement_priority ensalement_priority TINYINT(1) DEFAULT \'0\', CHANGE free_sections free_sections TINYINT(1) DEFAULT \'0\', CHANGE free_signup free_signup TINYINT(1) DEFAULT \'0\', CHANGE panel_submission_available panel_submission_available TINYINT(1) DEFAULT \'0\', CHANGE thesis_submission_available thesis_submission_available TINYINT(1) DEFAULT \'0\'');
    }
}

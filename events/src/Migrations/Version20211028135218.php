<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028135218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sub_dependent_example DROP FOREIGN KEY sub_dependent_example_ibfk_1');
        $this->addSql('ALTER TABLE example DROP FOREIGN KEY example_ibfk_1');
        $this->addSql('CREATE TABLE theme_submission_config (id BIGINT AUTO_INCREMENT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, year SMALLINT DEFAULT NULL, available_from DATE DEFAULT NULL, available_until DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE dependent_example');
        $this->addSql('DROP TABLE example');
        $this->addSql('DROP TABLE sub_dependent_example');
        $this->addSql('DROP TABLE user_institution');
        $this->addSql('ALTER TABLE user_themes ADD theme_submission_config BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_themes ADD CONSTRAINT FK_701029E3EA5B8462 FOREIGN KEY (theme_submission_config) REFERENCES theme_submission_config (id)');
        $this->addSql('CREATE INDEX IDX_701029E3EA5B8462 ON user_themes (theme_submission_config)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

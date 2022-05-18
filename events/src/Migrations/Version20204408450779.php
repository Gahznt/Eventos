<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204408450779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
            ALTER TABLE user_themes DROP portuguese_description;
            ALTER TABLE user_themes DROP english_description;
            ALTER TABLE user_themes DROP spanish_description;
            ALTER TABLE user_themes DROP portuguese_title;
            ALTER TABLE user_themes DROP english_title;
            ALTER TABLE user_themes DROP spanish_title;
            ALTER TABLE user_themes DROP portuguese_keywords;
            ALTER TABLE user_themes DROP english_keywords;
            ALTER TABLE user_themes DROP spanish_keywords;
            ALTER TABLE user_themes CHANGE status_evaluation status tinyint(4) NULL;
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

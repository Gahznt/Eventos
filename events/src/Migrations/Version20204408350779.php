<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204408350779 extends AbstractMigration
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
            INSERT INTO user_themes_details
                (user_themes_id, portuguese_description, english_description, spanish_description, 
                portuguese_title, english_title, spanish_title, portuguese_keywords, english_keywords, spanish_keywords, created_at)
            SELECT
                id, portuguese_description, english_description, spanish_description, 
                portuguese_title, english_title, spanish_title, portuguese_keywords, english_keywords, spanish_keywords, now()
            FROM
                user_themes;
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

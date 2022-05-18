<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220118142616 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_articles ADD modality_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D5682D6D889B FOREIGN KEY (modality_id) REFERENCES modality (id)');
        $this->addSql('CREATE INDEX IDX_5F50D5682D6D889B ON user_articles (modality_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_articles DROP FOREIGN KEY FK_5F50D5682D6D889B');
        $this->addSql('DROP INDEX IDX_5F50D5682D6D889B ON user_articles');
        $this->addSql('ALTER TABLE user_articles DROP modality_id');
    }
}

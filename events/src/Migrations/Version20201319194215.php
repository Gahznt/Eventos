<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201319194215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user ADD created_at DATE DEFAULT NULL AFTER expired_at');
        $this->addSql('ALTER TABLE user CHANGE expired_at expired_at DATETIME  NULL  DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME  NULL  DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE last_pay last_pay DATETIME  NULL  DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE updated_at updated_at DATETIME  NULL  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE user_academics CHANGE institution_id institution_id INT(10)  UNSIGNED  NULL');
        $this->addSql('ALTER TABLE user_academics CHANGE program_id program_id INT(10)  UNSIGNED  NULL');

    }

    public function down(Schema $schema) : void
    {
    }
}

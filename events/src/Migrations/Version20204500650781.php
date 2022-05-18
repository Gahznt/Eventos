<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650781 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Exclui dados p/ recriar FKs com segurança
        $this->addSql("TRUNCATE `user_evaluation_articles`;");

        // Recria FK user_themes
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `theme_first_id` BIGINT(20) unsigned NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` MODIFY COLUMN `theme_second_id` BIGINT(20) unsigned NULL;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` ADD FOREIGN KEY (`theme_first_id`) REFERENCES `user_themes` (`id`);");
        $this->addSql("ALTER TABLE `user_evaluation_articles` ADD FOREIGN KEY (`theme_second_id`) REFERENCES `user_themes` (`id`);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

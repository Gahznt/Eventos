<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210217182246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_articles` ADD `user_deleted_id` BIGINT UNSIGNED DEFAULT NULL AFTER `user_id`;");
        $this->addSql("ALTER TABLE `user_articles` ADD CONSTRAINT `user_articles_ibfk_9` FOREIGN KEY (`user_deleted_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

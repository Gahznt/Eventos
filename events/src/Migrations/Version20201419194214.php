<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201419194214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_articles_authors (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,user_articles_id bigint(20) unsigned DEFAULT NULL,user_author_id bigint(20) unsigned DEFAULT NULL,order_author smallint(5) DEFAULT \'1\',
          PRIMARY KEY (id),
          KEY user_articles_id (user_articles_id),
          KEY user_author_id (user_author_id),
          CONSTRAINT user_articles_authors_ibfk_1 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id),
          CONSTRAINT user_articles_authors_ibfk_2 FOREIGN KEY (user_author_id) REFERENCES user (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE user_articles_authors');
    }
}

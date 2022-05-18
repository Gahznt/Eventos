<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210429232821 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `system_ensalement_sessions_id` bigint(20) unsigned NULL after `system_ensalement_slots_id`;");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD CONSTRAINT `system_ensalement_scheduling_session_id` FOREIGN KEY (`system_ensalement_sessions_id`) REFERENCES `system_ensalement_sessions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

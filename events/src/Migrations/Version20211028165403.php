<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028165403 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

ALTER TABLE `activities_guest` DROP FOREIGN KEY `activities_guest_guest_id`;
ALTER TABLE `activities_guest` DROP FOREIGN KEY `activities_guest_ibfk_1`;
ALTER TABLE `activities_panelist` DROP FOREIGN KEY `activities_panelist_ibfk_1`;
ALTER TABLE `activities_panelist` DROP FOREIGN KEY `activities_panelist_panelist_id`;
ALTER TABLE `activity` DROP FOREIGN KEY `activity_ibfk_1`;
ALTER TABLE `activity` DROP FOREIGN KEY `division_id`;
ALTER TABLE `certificate` DROP FOREIGN KEY `FK_219CDA4A74281A5E`;
ALTER TABLE `certificate` DROP FOREIGN KEY `FK_219CDA4AA76ED395`;
ALTER TABLE `certificates_activities` DROP FOREIGN KEY `FK_EC856A3381C06096`;
ALTER TABLE `certificates_activities` DROP FOREIGN KEY `FK_EC856A3399223FFD`;
ALTER TABLE `certificates_divisions` DROP FOREIGN KEY `FK_C63CCD4D41859289`;
ALTER TABLE `certificates_divisions` DROP FOREIGN KEY `FK_C63CCD4D99223FFD`;
ALTER TABLE `certificates_panels` DROP FOREIGN KEY `FK_895A5C0C6F6FCB26`;
ALTER TABLE `certificates_panels` DROP FOREIGN KEY `FK_895A5C0C99223FFD`;
ALTER TABLE `certificates_theses` DROP FOREIGN KEY `FK_1B39FE168D82738`;
ALTER TABLE `certificates_theses` DROP FOREIGN KEY `FK_1B39FE199223FFD`;
ALTER TABLE `certificates_user_articles` DROP FOREIGN KEY `FK_37B10A9D99223FFD`;
ALTER TABLE `certificates_user_articles` DROP FOREIGN KEY `FK_37B10A9DB7A13F59`;
ALTER TABLE `certificates_user_themes` DROP FOREIGN KEY `FK_1FF92AC794142436`;
ALTER TABLE `certificates_user_themes` DROP FOREIGN KEY `FK_1FF92AC799223FFD`;
ALTER TABLE `city` DROP FOREIGN KEY `city_ibfk_1`;
ALTER TABLE `city` DROP FOREIGN KEY `city_ibfk_2`;
ALTER TABLE `course` DROP FOREIGN KEY `course_ibfk_1`;
ALTER TABLE `course` DROP FOREIGN KEY `course_ibfk_2`;
ALTER TABLE `division_coordinator` DROP FOREIGN KEY `division_coordinator_ibfk_1`;
ALTER TABLE `division_coordinator` DROP FOREIGN KEY `division_coordinator_ibfk_2`;
ALTER TABLE `division_coordinator` DROP FOREIGN KEY `division_coordinator_ibfk_3`;
ALTER TABLE `edition` DROP FOREIGN KEY `edition_event_id`;
ALTER TABLE `edition_discount` DROP FOREIGN KEY `edition_discount_edition_id`;
ALTER TABLE `edition_file` DROP FOREIGN KEY `edition_file_ibfk_1`;
ALTER TABLE `edition_payment_mode` DROP FOREIGN KEY `edition_payment_mode_edition_id`;
ALTER TABLE `edition_signup` DROP FOREIGN KEY `edition_signup_edition_discount_id`;
ALTER TABLE `edition_signup` DROP FOREIGN KEY `edition_signup_ibfk_1`;
ALTER TABLE `edition_signup` DROP FOREIGN KEY `edition_signup_ibfk_2`;
ALTER TABLE `edition_signup` DROP FOREIGN KEY `edition_signup_ibfk_3`;
ALTER TABLE `edition_signup` DROP FOREIGN KEY `edition_sign_up_free_individual_association_division_id`;
ALTER TABLE `edition_signup` DROP FOREIGN KEY `free_individual_association_user_association_id`;
ALTER TABLE `edition_signup_articles` DROP FOREIGN KEY `edition_signup_articles_ibfk_1`;
ALTER TABLE `edition_signup_articles` DROP FOREIGN KEY `edition_signup_articles_ibfk_2`;
ALTER TABLE `event_divisions` DROP FOREIGN KEY `event_divisions_division_id`;
ALTER TABLE `event_divisions` DROP FOREIGN KEY `event_divisions_event_id`;
ALTER TABLE `institution` DROP FOREIGN KEY `institution_ibfk_1`;
ALTER TABLE `keyword` DROP FOREIGN KEY `keyword_ibfk_1`;
ALTER TABLE `keyword` DROP FOREIGN KEY `keyword_ibfk_2`;
ALTER TABLE `panel` DROP FOREIGN KEY `panel_ibfk_1`;
ALTER TABLE `panel` DROP FOREIGN KEY `panel_ibfk_2`;
ALTER TABLE `panel` DROP FOREIGN KEY `panel_ibfk_3`;
ALTER TABLE `panel_evaluation_log` DROP FOREIGN KEY `panel_evaluation_log_ibfk_1`;
ALTER TABLE `panel_evaluation_log` DROP FOREIGN KEY `panel_evaluation_log_ibfk_2`;
ALTER TABLE `panels_panelist` DROP FOREIGN KEY `panels_panelist_ibfk_1`;
ALTER TABLE `panels_panelist` DROP FOREIGN KEY `panels_panelist_ibfk_2`;
ALTER TABLE `payment_user_association` DROP FOREIGN KEY `payment_user_id`;
ALTER TABLE `payment_user_association_details` DROP FOREIGN KEY `payment_user_association_fk`;
ALTER TABLE `program` DROP FOREIGN KEY `program_institution_id`;
ALTER TABLE `speaker` DROP FOREIGN KEY `speaker_ibfk_1`;
ALTER TABLE `state` DROP FOREIGN KEY `state_ibfk_1`;
ALTER TABLE `subsection` DROP FOREIGN KEY `subsection_edition`;
ALTER TABLE `system_ensalement_rooms` DROP FOREIGN KEY `system_ensalement_rooms_ibfk_1`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_1`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_10`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_11`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_2`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_4`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_5`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_6`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_7`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_8`;
ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_session_id`;
ALTER TABLE `system_ensalement_scheduling_articles` DROP FOREIGN KEY `system_ensalement_scheduling_articles_ibfk_1`;
ALTER TABLE `system_ensalement_scheduling_articles` DROP FOREIGN KEY `system_ensalement_scheduling_articles_ibfk_2`;
ALTER TABLE `system_ensalement_sessions` DROP FOREIGN KEY `system_ensalement_sessions_ibfk_1`;
ALTER TABLE `system_ensalement_slots` DROP FOREIGN KEY `system_ensalement_slots_ibfk_1`;
ALTER TABLE `system_ensalement_slots` DROP FOREIGN KEY `system_ensalement_slots_ibfk_2`;
ALTER TABLE `system_ensalement_slots` DROP FOREIGN KEY `system_ensalement_slots_ibfk_3`;
ALTER TABLE `system_evaluation` DROP FOREIGN KEY `system_evaluation_ibfk_1`;
ALTER TABLE `system_evaluation` DROP FOREIGN KEY `system_evaluation_ibfk_2`;
ALTER TABLE `system_evaluation_averages` DROP FOREIGN KEY `system_evaluation_averages_ibfk_1`;
ALTER TABLE `system_evaluation_averages` DROP FOREIGN KEY `system_evaluation_averages_ibfk_2`;
ALTER TABLE `system_evaluation_averages` DROP FOREIGN KEY `system_evaluation_averages_ibfk_3`;
ALTER TABLE `system_evaluation_averages_articles` DROP FOREIGN KEY `system_evaluation_averages_articles_ibfk_1`;
ALTER TABLE `system_evaluation_averages_articles` DROP FOREIGN KEY `system_evaluation_averages_articles_ibfk_2`;
ALTER TABLE `system_evaluation_config` DROP FOREIGN KEY `system_evaluation_config_ibfk_1`;
ALTER TABLE `system_evaluation_config` DROP FOREIGN KEY `system_evaluation_config_ibfk_3`;
ALTER TABLE `system_evaluation_log` DROP FOREIGN KEY `system_evaluation_log_ibfk_1`;
ALTER TABLE `system_evaluation_log` DROP FOREIGN KEY `system_evaluation_log_ibfk_2`;
ALTER TABLE `theme` DROP FOREIGN KEY `theme_ibfk_1`;
ALTER TABLE `theme` DROP FOREIGN KEY `theme_ibfk_2`;
ALTER TABLE `thesis` DROP FOREIGN KEY `thesis_division_id`;
ALTER TABLE `thesis` DROP FOREIGN KEY `thesis_edition_id`;
ALTER TABLE `thesis` DROP FOREIGN KEY `thesis_user_id`;
ALTER TABLE `thesis` DROP FOREIGN KEY `thesis_user_themes_id`;
ALTER TABLE `user` DROP FOREIGN KEY `user_ibfk_1`;
ALTER TABLE `user_academics` DROP FOREIGN KEY `user_academics_ibfk_3`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `original_user_themes_id`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_1`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_2`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_3`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_5`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_6`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_8`;
ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_9`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_ibfk_1`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_ibfk_2`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_institution_first_id`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_institution_second_id`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_program_first_id`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_program_second_id`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_state_first_id`;
ALTER TABLE `user_articles_authors` DROP FOREIGN KEY `user_articles_authors_state_second_id`;
ALTER TABLE `user_articles_files` DROP FOREIGN KEY `user_articles_files_ibfk_1`;
ALTER TABLE `user_articles_keywords` DROP FOREIGN KEY `user_articles_keywords_ibfk_1`;
ALTER TABLE `user_articles_keywords` DROP FOREIGN KEY `user_articles_keywords_ibfk_2`;
ALTER TABLE `user_association` DROP FOREIGN KEY `fk_ua_division`;
ALTER TABLE `user_association` DROP FOREIGN KEY `fk_ua_instit`;
ALTER TABLE `user_association` DROP FOREIGN KEY `fk_ua_prog`;
ALTER TABLE `user_association` DROP FOREIGN KEY `fk_ua_user`;
ALTER TABLE `user_association_divisions` DROP FOREIGN KEY `fk_uad_div`;
ALTER TABLE `user_association_divisions` DROP FOREIGN KEY `fk_uad_ua`;
ALTER TABLE `user_committee` DROP FOREIGN KEY `user_committee_ibfk_1`;
ALTER TABLE `user_committee` DROP FOREIGN KEY `user_committee_ibfk_2`;
ALTER TABLE `user_committee` DROP FOREIGN KEY `user_committee_ibfk_3`;
ALTER TABLE `user_consents` DROP FOREIGN KEY `user_consents_ibfk_1`;
ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568371C5171`;
ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568A76ED395`;
ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568B5565F6D`;
ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `user_evaluation_articles_ibfk_1`;
ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `user_evaluation_articles_ibfk_2`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554A69789E6A`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554AA76ED395`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554AA9A4EA13`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554AB71BDEE1`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554AD7BE04F8`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554AE83FC083`;
ALTER TABLE `user_institutions_programs` DROP FOREIGN KEY `FK_5D45554AF6286EC1`;
ALTER TABLE `user_methods` DROP FOREIGN KEY `user_methods_ibfk_2`;
ALTER TABLE `user_methods` DROP FOREIGN KEY `user_methods_ibfk_3`;
ALTER TABLE `user_theme_keyword` DROP FOREIGN KEY `user_theme_keyword_fk_3`;
ALTER TABLE `user_theme_keyword` DROP FOREIGN KEY `user_theme_keyword_fk_4`;
ALTER TABLE `user_themes` DROP FOREIGN KEY `FK_701029E3EA5B8462`;
ALTER TABLE `user_themes` DROP FOREIGN KEY `user_id`;
ALTER TABLE `user_themes` DROP FOREIGN KEY `user_themes_ibfk_1`;
ALTER TABLE `user_themes_bibliographies` DROP FOREIGN KEY `user_themes_bibliographies_ibfk_1`;
ALTER TABLE `user_themes_details` DROP FOREIGN KEY `user_themes_details_ibfk_1`;
ALTER TABLE `user_themes_evaluation_log` DROP FOREIGN KEY `user_themes_evaluation_log_ibfk_1`;
ALTER TABLE `user_themes_evaluation_log` DROP FOREIGN KEY `user_themes_evaluation_log_ibfk_2`;
ALTER TABLE `user_themes_researchers` DROP FOREIGN KEY `user_themes_researchers_ibfk_1`;
ALTER TABLE `user_themes_researchers` DROP FOREIGN KEY `user_themes_researchers_ibfk_2`;
ALTER TABLE `user_themes_reviewers` DROP FOREIGN KEY `user_themes_reviewers_ibfk_1`;
ALTER TABLE `user_theories` DROP FOREIGN KEY `user_theory_fk_3`;
ALTER TABLE `user_theories` DROP FOREIGN KEY `user_theory_fk_4`;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

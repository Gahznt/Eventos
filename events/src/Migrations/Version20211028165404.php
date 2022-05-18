<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028165404 extends AbstractMigration
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
ALTER TABLE activity CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE time_restriction time_restriction VARCHAR(255) NOT NULL
    , CHANGE is_global is_global TINYINT(1) DEFAULT NULL;

ALTER TABLE activities_guest CHANGE activity_id activity_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE activities_panelist CHANGE activity_id activity_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE panelist_id panelist_id BIGINT UNSIGNED DEFAULT NULL;

ALTER TABLE certificate CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE certificates_divisions CHANGE division_id division_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE city CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE state_id state_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE country_id country_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE created_at created_at DATETIME DEFAULT NULL
    , CHANGE updated_on updated_on DATETIME DEFAULT NULL;
ALTER TABLE country CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE name_english name_english VARCHAR(255) NOT NULL
    , CHANGE updated_at updated_at DATETIME DEFAULT NULL;
ALTER TABLE course CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE institution_id institution_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE program_id program_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE division CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE division_coordinator CHANGE coordinator_id coordinator_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE edition CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE event_id event_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE edition_discount CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE edition_file CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE description description LONGTEXT NOT NULL;
ALTER TABLE edition_payment_mode CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE name name VARCHAR(255) NOT NULL;
ALTER TABLE edition_signup CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE joined_id joined_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE edition_payment_mode_id edition_payment_mode_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE edition_discount_id edition_discount_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE free_individual_association_division_id free_individual_association_division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE free_individual_association_user_association_id free_individual_association_user_association_id INT DEFAULT NULL
    , CHANGE badge badge VARCHAR(255) NOT NULL
    , CHANGE initial_institute initial_institute VARCHAR(255) NOT NULL
    , CHANGE status_pay status_pay TINYINT(1) DEFAULT NULL;
ALTER TABLE edition_signup_articles MODIFY id BIGINT UNSIGNED NOT NULL;
ALTER TABLE edition_signup_articles DROP PRIMARY KEY;
ALTER TABLE edition_signup_articles DROP id, DROP created_at, DROP updated_at, DROP deleted_at;
ALTER TABLE edition_signup_articles ADD PRIMARY KEY (edition_signup_id, user_articles_id);
ALTER TABLE event CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE name_portuguese name_portuguese VARCHAR(255) NOT NULL
    , CHANGE title_portuguese title_portuguese VARCHAR(255) NOT NULL
    , CHANGE name_english name_english VARCHAR(255) NOT NULL
    , CHANGE title_english title_english VARCHAR(255) NOT NULL
    , CHANGE name_spanish name_spanish VARCHAR(255) NOT NULL
    , CHANGE title_spanish title_spanish VARCHAR(255) NOT NULL
    , CHANGE status status SMALLINT DEFAULT NULL
    , CHANGE is_homolog is_homolog TINYINT(1) DEFAULT NULL
    , CHANGE number_words number_words SMALLINT NOT NULL;
ALTER TABLE event_divisions MODIFY id INT UNSIGNED NOT NULL;
ALTER TABLE event_divisions DROP PRIMARY KEY;
ALTER TABLE event_divisions DROP id
    , CHANGE event_id event_id BIGINT UNSIGNED NOT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE event_divisions ADD PRIMARY KEY (event_id, division_id);
ALTER TABLE institution CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE city_id city_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE keyword CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE method CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE panel CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE language language TINYINT(1) DEFAULT NULL
    , CHANGE status_evaluation status_evaluation SMALLINT DEFAULT NULL
    , CHANGE created_at created_at DATETIME DEFAULT NULL;
    
ALTER TABLE panel_evaluation_log CHANGE panel_id panel_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE panel_evaluation_log CHANGE created_at created_at DATETIME DEFAULT NULL;

ALTER TABLE panels_panelist CHANGE panel_id panel_id BIGINT UNSIGNED DEFAULT NULL;

ALTER TABLE payment_user_association CHANGE id id INT AUTO_INCREMENT NOT NULL;
ALTER TABLE payment_user_association_details CHANGE id id INT AUTO_INCREMENT NOT NULL
    , CHANGE payment_user_association_id payment_user_association_id INT NOT NULL
    , CHANGE bank_slip_amount bank_slip_amount DOUBLE PRECISION NOT NULL
    , CHANGE fee_amount fee_amount DOUBLE PRECISION NOT NULL
    , CHANGE net_amount net_amount DOUBLE PRECISION NOT NULL
    , CHANGE due_date due_date DATE DEFAULT NULL;
ALTER TABLE program CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE institution_id institution_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE email email VARCHAR(255) NOT NULL
    , CHANGE website website VARCHAR(255) NOT NULL
    , CHANGE street street VARCHAR(255) NOT NULL
    , CHANGE zipcode zipcode VARCHAR(255) NOT NULL
    , CHANGE number number VARCHAR(255) NOT NULL
    , CHANGE complement complement VARCHAR(255) NOT NULL
    , CHANGE neighborhood neighborhood VARCHAR(255) NOT NULL
    , CHANGE coordinator coordinator VARCHAR(255) NOT NULL;
ALTER TABLE speaker CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE type type SMALLINT NOT NULL COMMENT \'0 = Nacional, 1 = Internacional\'
    , CHANGE status status SMALLINT DEFAULT NULL
    , CHANGE is_homolog is_homolog SMALLINT DEFAULT NULL
    , CHANGE name_portuguese name_portuguese VARCHAR(255) NOT NULL;
ALTER TABLE state CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE country_id country_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE updated_at updated_at DATETIME DEFAULT NULL;
ALTER TABLE subsection CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE type type VARCHAR(255) NOT NULL
    , CHANGE is_highlight is_highlight SMALLINT DEFAULT NULL
    , CHANGE name_portuguese name_portuguese VARCHAR(255) NOT NULL
    , CHANGE front_call_portuguese front_call_portuguese VARCHAR(255) NOT NULL
    , CHANGE description_portuguese description_portuguese TEXT NOT NULL
    , CHANGE name_english name_english VARCHAR(255) NOT NULL
    , CHANGE front_call_english front_call_english VARCHAR(255) NOT NULL
    , CHANGE description_english description_english TEXT NOT NULL
    , CHANGE name_spanish name_spanish VARCHAR(255) NOT NULL
    , CHANGE front_call_spanish front_call_spanish VARCHAR(255) NOT NULL
    , CHANGE description_spanish description_spanish TEXT NOT NULL
    , CHANGE status status SMALLINT DEFAULT NULL
    , CHANGE is_homolog is_homolog SMALLINT DEFAULT NULL;
ALTER TABLE system_ensalement_rooms CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE system_ensalement_scheduling CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE system_ensalement_slots_id system_ensalement_slots_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE content_type content_type SMALLINT UNSIGNED DEFAULT NULL COMMENT \'1=activity, 2=panel\'
    , CHANGE accept accept TINYINT(1) DEFAULT NULL
    , CHANGE priority priority TINYINT(1) DEFAULT NULL;

ALTER TABLE system_ensalement_scheduling_articles CHANGE system_ensalement_sheduling_id system_ensalement_sheduling_id BIGINT UNSIGNED DEFAULT NULL;

ALTER TABLE system_ensalement_sessions CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE system_ensalement_slots CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE system_ensalement_sessions_id system_ensalement_sessions_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE system_ensalement_rooms_id system_ensalement_rooms_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE system_evaluation CHANGE criteria_one criteria_one VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_two criteria_two VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_three criteria_three VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_four criteria_four VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_five criteria_five VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_six criteria_six VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_seven criteria_seven VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_eight criteria_eight VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_nine criteria_nine VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_ten criteria_ten VARCHAR(10) DEFAULT NULL
    , CHANGE criteria_final criteria_final INT DEFAULT NULL
    , CHANGE is_author_rated is_author_rated TINYINT(1) DEFAULT NULL;

ALTER TABLE system_evaluation CHANGE user_owner_id user_owner_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE user_articles_id user_articles_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE system_evaluation_averages_articles CHANGE user_articles_id user_articles_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE system_evaluation_averages_id system_evaluation_averages_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE system_evaluation_averages CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE point_primary point_primary NUMERIC(10, 0) DEFAULT NULL
    , CHANGE point_secondary point_secondary NUMERIC(10, 0) DEFAULT NULL;
ALTER TABLE system_evaluation_config CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE system_evaluation_indications CHANGE user_articles_id user_articles_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE user_evaluator_id user_evaluator_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE system_evaluation_log CHANGE sytem_evaluation_id sytem_evaluation_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE status status SMALLINT DEFAULT NULL;
    
ALTER TABLE theme CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE theme_submission_config CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE theory CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE thesis CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE status status SMALLINT DEFAULT NULL
    , CHANGE confirmed confirmed SMALLINT DEFAULT NULL;
ALTER TABLE user CHANGE city_id city_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE user_methods CHANGE user_id user_id BIGINT UNSIGNED NOT NULL
    , CHANGE method_id method_id BIGINT UNSIGNED NOT NULL;
                             
ALTER TABLE user_theories CHANGE user_id user_id BIGINT UNSIGNED NOT NULL
    , CHANGE theory_id theory_id BIGINT UNSIGNED NOT NULL;
                             
ALTER TABLE user_academics DROP area, DROP institution_id, DROP other_institution, DROP program_id, DROP other_program, DROP start_date, DROP end_date;
ALTER TABLE user_articles CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE method_id method_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE theory_id theory_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE last_id last_id INT DEFAULT NULL
    , CHANGE portuguese portuguese TINYINT(1) DEFAULT NULL
    , CHANGE english english TINYINT(1) DEFAULT NULL
    , CHANGE spanish spanish TINYINT(1) DEFAULT NULL
    , CHANGE job_complete job_complete TINYINT(1) DEFAULT NULL
    , CHANGE resume_flag resume_flag TINYINT(1) DEFAULT NULL
    , CHANGE rac_bar rac_bar TINYINT(1) DEFAULT NULL
    , CHANGE never_publish never_publish TINYINT(1) DEFAULT NULL
    , CHANGE confirm_files_correct confirm_files_correct TINYINT(1) DEFAULT NULL
    , CHANGE language language INT DEFAULT NULL
    , CHANGE frame frame INT DEFAULT NULL
    , CHANGE premium premium TINYINT(1) DEFAULT NULL
    , CHANGE status status SMALLINT DEFAULT NULL
    , CHANGE is_published is_published TINYINT(1) DEFAULT NULL;
ALTER TABLE user_articles_authors CHANGE state_first_id state_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE state_second_id state_second_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE institution_first_id institution_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE institution_second_id institution_second_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE program_first_id program_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE program_second_id program_second_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE user_articles_keywords CHANGE user_articles_id user_articles_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE keyword_id keyword_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE user_articles_files CHANGE user_articles_id user_articles_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE created_at created_at DATETIME DEFAULT NULL;
    
ALTER TABLE user_association CHANGE id id INT AUTO_INCREMENT NOT NULL
    , CHANGE institution_id institution_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE program_id program_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE type type INT NOT NULL
    , CHANGE last_pay last_pay DATETIME NOT NULL
    , CHANGE level level INT NOT NULL
    , CHANGE value value DOUBLE PRECISION NOT NULL
    , CHANGE status_pay status_pay INT NOT NULL;
ALTER TABLE user_association_divisions DROP PRIMARY KEY;
ALTER TABLE user_association_divisions DROP id
    , CHANGE user_association_id user_association_id INT NOT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED NOT NULL;
ALTER TABLE user_association_divisions ADD PRIMARY KEY (user_association_id, division_id);

ALTER TABLE user_association_divisions MODIFY id BIGINT UNSIGNED NOT NULL;

ALTER TABLE user_association_divisions CHANGE user_association_id user_association_id INT NOT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED NOT NULL

ALTER TABLE user_committee CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL;
    
ALTER TABLE user_consents CHANGE status status VARCHAR(255) DEFAULT NULL;
ALTER TABLE user_evaluation_articles CHANGE division_first_id division_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE division_second_id division_second_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE want_evaluate want_evaluate TINYINT(1) DEFAULT NULL;
    
ALTER TABLE user_institutions_programs CHANGE state_first_id state_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE state_second_id state_second_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE institution_first_id institution_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE institution_second_id institution_second_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE program_first_id program_first_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE program_second_id program_second_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE user_theme_keyword CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL
    , CHANGE keyword_id keyword_id BIGINT UNSIGNED DEFAULT NULL;
ALTER TABLE user_themes CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL
    , CHANGE theme_submission_config theme_submission_config BIGINT UNSIGNED DEFAULT NULL
    , CHANGE status status INT DEFAULT NULL
    , CHANGE position position INT DEFAULT 1 NOT NULL;
ALTER TABLE user_themes_bibliographies CHANGE name name VARCHAR(255) NOT NULL;
ALTER TABLE user_themes_evaluation_log CHANGE created_at created_at DATETIME DEFAULT NULL
    , CHANGE ip ip VARCHAR(255) DEFAULT NULL
    , CHANGE action action VARCHAR(255) DEFAULT NULL
    , CHANGE reason reason VARCHAR(255) DEFAULT NULL;
ALTER TABLE user_themes_reviewers CHANGE name name VARCHAR(255) NOT NULL
    , CHANGE link_lattes link_lattes VARCHAR(255) NOT NULL
    , CHANGE email email VARCHAR(255) NOT NULL
    , CHANGE institute institute VARCHAR(255) NOT NULL
    , CHANGE program program VARCHAR(255) NOT NULL
    , CHANGE state state VARCHAR(255) NOT NULL;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

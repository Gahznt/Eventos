<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028205122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities_guest ADD CONSTRAINT FK_6F3EB9E69A4AA658 FOREIGN KEY (guest_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE activities_guest ADD CONSTRAINT FK_6F3EB9E681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE activities_panelist ADD CONSTRAINT FK_47117FF481C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE activities_panelist ADD CONSTRAINT FK_47117FF47E8B14D FOREIGN KEY (panelist_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certificates_user_articles ADD CONSTRAINT FK_37B10A9D99223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id)');
        $this->addSql('ALTER TABLE certificates_user_articles ADD CONSTRAINT FK_37B10A9DB7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE certificates_user_themes ADD CONSTRAINT FK_1FF92AC799223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id)');
        $this->addSql('ALTER TABLE certificates_user_themes ADD CONSTRAINT FK_1FF92AC794142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE certificates_activities ADD CONSTRAINT FK_EC856A3399223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id)');
        $this->addSql('ALTER TABLE certificates_activities ADD CONSTRAINT FK_EC856A3381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE certificates_panels ADD CONSTRAINT FK_895A5C0C99223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id)');
        $this->addSql('ALTER TABLE certificates_panels ADD CONSTRAINT FK_895A5C0C6F6FCB26 FOREIGN KEY (panel_id) REFERENCES panel (id)');
        $this->addSql('ALTER TABLE certificates_theses ADD CONSTRAINT FK_1B39FE199223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id)');
        $this->addSql('ALTER TABLE certificates_theses ADD CONSTRAINT FK_1B39FE168D82738 FOREIGN KEY (thesis_id) REFERENCES thesis (id)');
        $this->addSql('ALTER TABLE certificates_divisions ADD CONSTRAINT FK_C63CCD4D99223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id)');
        $this->addSql('ALTER TABLE certificates_divisions ADD CONSTRAINT FK_C63CCD4D41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B02345D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB910405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB93EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE division_coordinator ADD CONSTRAINT FK_D29CBE9BE7877946 FOREIGN KEY (coordinator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE division_coordinator ADD CONSTRAINT FK_D29CBE9B41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE division_coordinator ADD CONSTRAINT FK_D29CBE9B74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE edition ADD CONSTRAINT FK_A891181F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE edition_discount ADD CONSTRAINT FK_AFF7B85B74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE edition_file ADD CONSTRAINT FK_3CA8DD0E74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE edition_payment_mode ADD CONSTRAINT FK_D7673E2A74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE edition_signup ADD CONSTRAINT FK_AD3AAAF730576121 FOREIGN KEY (free_individual_association_division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE edition_signup ADD CONSTRAINT FK_AD3AAAF796D47C1 FOREIGN KEY (edition_discount_id) REFERENCES edition_discount (id)');
        $this->addSql('ALTER TABLE edition_signup ADD CONSTRAINT FK_AD3AAAF776C94ED4 FOREIGN KEY (joined_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE edition_signup ADD CONSTRAINT FK_AD3AAAF7C188E957 FOREIGN KEY (edition_payment_mode_id) REFERENCES edition_payment_mode (id)');
        $this->addSql('ALTER TABLE edition_signup ADD CONSTRAINT FK_AD3AAAF774281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE edition_signup ADD CONSTRAINT FK_AD3AAAF73346BFDA FOREIGN KEY (free_individual_association_user_association_id) REFERENCES user_association (id)');
        $this->addSql('ALTER TABLE edition_signup_articles ADD CONSTRAINT FK_F196D933442E2D19 FOREIGN KEY (edition_signup_id) REFERENCES edition_signup (id)');
        $this->addSql('ALTER TABLE edition_signup_articles ADD CONSTRAINT FK_F196D933B7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE event_divisions ADD CONSTRAINT FK_8751320C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_divisions ADD CONSTRAINT FK_8751320C41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE institution CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE cellphone cellphone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE institution ADD CONSTRAINT FK_3A9F98E58BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE keyword ADD CONSTRAINT FK_5A93713B41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE keyword ADD CONSTRAINT FK_5A93713B59027487 FOREIGN KEY (theme_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE panel ADD CONSTRAINT FK_A2ADD30F41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE panel ADD CONSTRAINT FK_A2ADD30F74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE panel ADD CONSTRAINT FK_A2ADD30FF8BAC902 FOREIGN KEY (proponent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panel_evaluation_log ADD CONSTRAINT FK_EB0C33086F6FCB26 FOREIGN KEY (panel_id) REFERENCES panel (id)');
        $this->addSql('ALTER TABLE panel_evaluation_log ADD CONSTRAINT FK_EB0C3308A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panels_panelist ADD CONSTRAINT FK_E9D3A9F36F6FCB26 FOREIGN KEY (panel_id) REFERENCES panel (id)');
        $this->addSql('ALTER TABLE panels_panelist ADD CONSTRAINT FK_E9D3A9F37E8B14D FOREIGN KEY (panelist_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment_user_association ADD CONSTRAINT FK_1B4692E7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment_user_association_details ADD CONSTRAINT FK_391DEE7816240F33 FOREIGN KEY (payment_user_association_id) REFERENCES payment_user_association (id)');
        $this->addSql('ALTER TABLE program CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE cellphone cellphone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED778410405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE speaker ADD CONSTRAINT FK_7B85DB6174281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE state ADD CONSTRAINT FK_A393D2FBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE subsection ADD CONSTRAINT FK_6611D22074281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE system_ensalement_rooms ADD CONSTRAINT FK_554F79BC74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC6F6FCB26 FOREIGN KEY (panel_id) REFERENCES panel (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC2229B35F FOREIGN KEY (system_ensalement_slots_id) REFERENCES system_ensalement_slots (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC88E52D25 FOREIGN KEY (system_ensalement_sessions_id) REFERENCES system_ensalement_sessions (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FC94142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FCE06D02EB FOREIGN KEY (user_register_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FCAE566ADF FOREIGN KEY (coordinator_debater_1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling ADD CONSTRAINT FK_B84B16FCBCE3C531 FOREIGN KEY (coordinator_debater_2_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling_articles ADD CONSTRAINT FK_EB9ECCB1C58A82E5 FOREIGN KEY (system_ensalement_sheduling_id) REFERENCES system_ensalement_scheduling (id)');
        $this->addSql('ALTER TABLE system_ensalement_scheduling_articles ADD CONSTRAINT FK_EB9ECCB1B7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE system_ensalement_sessions ADD CONSTRAINT FK_A6D8A91774281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE system_ensalement_slots ADD CONSTRAINT FK_E19A56FA74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE system_ensalement_slots ADD CONSTRAINT FK_E19A56FA88E52D25 FOREIGN KEY (system_ensalement_sessions_id) REFERENCES system_ensalement_sessions (id)');
        $this->addSql('ALTER TABLE system_ensalement_slots ADD CONSTRAINT FK_E19A56FAB29B5CAF FOREIGN KEY (system_ensalement_rooms_id) REFERENCES system_ensalement_rooms (id)');
        $this->addSql('ALTER TABLE system_evaluation ADD CONSTRAINT FK_A6F0B54CB7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE system_evaluation ADD CONSTRAINT FK_A6F0B54C9EB185F9 FOREIGN KEY (user_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_evaluation_averages ADD CONSTRAINT FK_113FE026A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_evaluation_averages ADD CONSTRAINT FK_113FE02674281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE system_evaluation_averages ADD CONSTRAINT FK_113FE02641859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE system_evaluation_averages_articles ADD CONSTRAINT FK_7587AEC5B7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE system_evaluation_averages_articles ADD CONSTRAINT FK_7587AEC5B29921A2 FOREIGN KEY (system_evaluation_averages_id) REFERENCES system_evaluation_averages (id)');
        $this->addSql('ALTER TABLE system_evaluation_config ADD CONSTRAINT FK_C42143B0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_evaluation_config ADD CONSTRAINT FK_C42143B074281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('CALL rename_index(\'system_evaluation_config\', \'event_id\', \'IDX_C42143B074281A5E\')');
        $this->addSql('ALTER TABLE system_evaluation_indications ADD CONSTRAINT FK_C19235A8B7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE system_evaluation_indications ADD CONSTRAINT FK_C19235A8C39B27E0 FOREIGN KEY (user_evaluator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE system_evaluation_log ADD CONSTRAINT FK_FB0A4D21E1E88338 FOREIGN KEY (sytem_evaluation_id) REFERENCES system_evaluation (id)');
        $this->addSql('ALTER TABLE system_evaluation_log ADD CONSTRAINT FK_FB0A4D218F3546BB FOREIGN KEY (user_log_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E70874281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E70841859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE thesis ADD CONSTRAINT FK_AF4FF3A841859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE thesis ADD CONSTRAINT FK_AF4FF3A874281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE thesis ADD CONSTRAINT FK_AF4FF3A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE thesis ADD CONSTRAINT FK_AF4FF3A894142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CALL rename_index(\'user\', \'fk_user_city_idx\', \'IDX_8D93D6498BAC62AF\')');
        $this->addSql('ALTER TABLE user_methods MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user_methods DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_methods ADD CONSTRAINT FK_EEFE963A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_methods ADD CONSTRAINT FK_EEFE96319883967 FOREIGN KEY (method_id) REFERENCES method (id)');
        $this->addSql('ALTER TABLE user_theories MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user_theories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_theories ADD CONSTRAINT FK_101FF28A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_theories ADD CONSTRAINT FK_101FF286441A32F FOREIGN KEY (theory_id) REFERENCES theory (id)');
        $this->addSql('ALTER TABLE user_academics ADD CONSTRAINT FK_15759E46A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D568A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D56838FAD351 FOREIGN KEY (user_deleted_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D56874281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D56841859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D56894142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D5686F0F098A FOREIGN KEY (original_user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D56819883967 FOREIGN KEY (method_id) REFERENCES method (id)');
        $this->addSql('ALTER TABLE user_articles ADD CONSTRAINT FK_5F50D5686441A32F FOREIGN KEY (theory_id) REFERENCES theory (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DB7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DF6957EFF FOREIGN KEY (user_author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DF6286EC1 FOREIGN KEY (institution_first_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DE83FC083 FOREIGN KEY (institution_second_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2D69789E6A FOREIGN KEY (program_first_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DA9A4EA13 FOREIGN KEY (program_second_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DD7BE04F8 FOREIGN KEY (state_first_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE user_articles_authors ADD CONSTRAINT FK_B67C0C2DB71BDEE1 FOREIGN KEY (state_second_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE user_articles_files ADD CONSTRAINT FK_8552B7F4B7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE user_articles_keywords ADD CONSTRAINT FK_F3D4F86F115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id)');
        $this->addSql('ALTER TABLE user_articles_keywords ADD CONSTRAINT FK_F3D4F86FB7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id)');
        $this->addSql('ALTER TABLE user_association CHANGE last_pay last_pay DATETIME DEFAULT NULL');
        $this->addSql('UPDATE `user_association` SET `last_pay` = NULL WHERE `user_association`.`last_pay` LIKE \'0000-00-00 00:00:00\'');
        $this->addSql('ALTER TABLE user_association ADD CONSTRAINT FK_549EE859A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_association ADD CONSTRAINT FK_549EE85910405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE user_association ADD CONSTRAINT FK_549EE8593EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE user_association ADD CONSTRAINT FK_549EE85941859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_association_divisions MODIFY id BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user_association_divisions DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_association_divisions CHANGE user_association_id user_association_id INT NOT NULL, CHANGE division_id division_id BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user_association_divisions ADD CONSTRAINT FK_6527483A843C17FC FOREIGN KEY (user_association_id) REFERENCES user_association (id)');
        $this->addSql('ALTER TABLE user_association_divisions ADD CONSTRAINT FK_6527483A41859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_association_divisions ADD PRIMARY KEY (user_association_id, division_id)');
        $this->addSql('ALTER TABLE user_committee CHANGE user_id user_id BIGINT UNSIGNED DEFAULT NULL, CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL, CHANGE edition_id edition_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE user_committee ADD CONSTRAINT FK_D2124FD3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_committee ADD CONSTRAINT FK_D2124FD374281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE user_committee ADD CONSTRAINT FK_D2124FD341859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_consents CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_consents ADD CONSTRAINT FK_E6572967A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_evaluation_articles CHANGE division_first_id division_first_id BIGINT UNSIGNED DEFAULT NULL, CHANGE division_second_id division_second_id BIGINT UNSIGNED DEFAULT NULL, CHANGE want_evaluate want_evaluate TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_evaluation_articles ADD CONSTRAINT FK_9850273CB5565F6D FOREIGN KEY (division_first_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_evaluation_articles ADD CONSTRAINT FK_9850273C371C5171 FOREIGN KEY (division_second_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_evaluation_articles ADD CONSTRAINT FK_9850273C4A737028 FOREIGN KEY (theme_first_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_evaluation_articles ADD CONSTRAINT FK_9850273C3155C141 FOREIGN KEY (theme_second_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_evaluation_articles ADD CONSTRAINT FK_9850273CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_institutions_programs CHANGE state_first_id state_first_id BIGINT UNSIGNED DEFAULT NULL, CHANGE state_second_id state_second_id BIGINT UNSIGNED DEFAULT NULL, CHANGE institution_first_id institution_first_id BIGINT UNSIGNED DEFAULT NULL, CHANGE institution_second_id institution_second_id BIGINT UNSIGNED DEFAULT NULL, CHANGE program_first_id program_first_id BIGINT UNSIGNED DEFAULT NULL, CHANGE program_second_id program_second_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554AD7BE04F8 FOREIGN KEY (state_first_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554AB71BDEE1 FOREIGN KEY (state_second_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554AF6286EC1 FOREIGN KEY (institution_first_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554AE83FC083 FOREIGN KEY (institution_second_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554A69789E6A FOREIGN KEY (program_first_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554AA9A4EA13 FOREIGN KEY (program_second_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE user_institutions_programs ADD CONSTRAINT FK_5D45554AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_theme_keyword CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE keyword_id keyword_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE user_theme_keyword ADD CONSTRAINT FK_ED7ED948115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id)');
        $this->addSql('ALTER TABLE user_theme_keyword ADD CONSTRAINT FK_ED7ED948A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_themes CHANGE division_id division_id BIGINT UNSIGNED DEFAULT NULL, CHANGE status status INT DEFAULT NULL, CHANGE position position INT DEFAULT 1 NOT NULL, CHANGE theme_submission_config theme_submission_config BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE user_themes ADD CONSTRAINT FK_701029E3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_themes ADD CONSTRAINT FK_701029E341859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE user_themes ADD CONSTRAINT FK_701029E3EA5B8462 FOREIGN KEY (theme_submission_config) REFERENCES theme_submission_config (id)');
        $this->addSql('ALTER TABLE user_themes_bibliographies CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE user_themes_id user_themes_id BIGINT UNSIGNED DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_themes_bibliographies ADD CONSTRAINT FK_C49293FD94142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_themes_details ADD CONSTRAINT FK_AB10566E94142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_themes_evaluation_log CHANGE user_themes_id user_themes_id BIGINT UNSIGNED DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE ip ip VARCHAR(255) DEFAULT NULL, CHANGE action action VARCHAR(255) DEFAULT NULL, CHANGE reason reason VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_themes_evaluation_log ADD CONSTRAINT FK_49D3AABD94142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_themes_evaluation_log ADD CONSTRAINT FK_49D3AABDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_themes_researchers CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE user_themes_id user_themes_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE user_themes_researchers ADD CONSTRAINT FK_317A5F7F94142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
        $this->addSql('ALTER TABLE user_themes_researchers ADD CONSTRAINT FK_317A5F7FC7533BDE FOREIGN KEY (researcher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_themes_reviewers CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE user_themes_id user_themes_id BIGINT UNSIGNED DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE link_lattes link_lattes VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE cellphone cellphone VARCHAR(255) DEFAULT NULL, CHANGE institute institute VARCHAR(255) NOT NULL, CHANGE program program VARCHAR(255) NOT NULL, CHANGE state state VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_themes_reviewers ADD CONSTRAINT FK_12FF7A1794142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

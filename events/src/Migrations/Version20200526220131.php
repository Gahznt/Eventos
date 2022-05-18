<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200526220131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE country (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, iso3 CHAR(3) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, iso2 CHAR(2) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, phonecode VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, capital VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, currency VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, native VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, emoji VARCHAR(191) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, emojiU VARCHAR(191) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, flag TINYINT(1) DEFAULT \'1\' NOT NULL, wikiDataId VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'Rapid API GeoDB Cities\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE state (id INT UNSIGNED AUTO_INCREMENT NOT NULL, country_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, country_code CHAR(2) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, fips_code VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, iso2 VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, flag TINYINT(1) DEFAULT \'1\' NOT NULL, wikiDataId VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'Rapid API GeoDB Cities\', INDEX country_region (country_id), PRIMARY KEY(id), CONSTRAINT `state_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city (id INT UNSIGNED AUTO_INCREMENT NOT NULL, state_id INT UNSIGNED DEFAULT NULL, country_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, state_code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, country_code CHAR(2) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, latitude NUMERIC(10, 8) NOT NULL, longitude NUMERIC(11, 8) NOT NULL, created_at DATETIME DEFAULT \'2014-01-01 01:01:01\' NOT NULL, updated_on DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, flag TINYINT(1) DEFAULT \'1\' NOT NULL, wikiDataId VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'Rapid API GeoDB Cities\', INDEX cities_test_ibfk_2 (country_id), INDEX cities_test_ibfk_1 (state_id), PRIMARY KEY(id), CONSTRAINT `city_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `state` (`id`),
        CONSTRAINT `city_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE institution (id INT UNSIGNED AUTO_INCREMENT NOT NULL, city_id INT UNSIGNED DEFAULT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci` COMMENT \'prirmaria ou secundaria definida em código\', type TINYINT(1) NOT NULL, paid TINYINT(1) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, phone INT NOT NULL, cellphone INT NOT NULL, email VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, website VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, street VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, zipcode VARCHAR(9) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, number INT NOT NULL, complement VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, neighborhood VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, coordinator VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_inst_city_idx (city_id), PRIMARY KEY(id), CONSTRAINT `institution_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE program (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, paid TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE course (id int(10) unsigned NOT NULL AUTO_INCREMENT,institution_id int(10) unsigned DEFAULT NULL,program_id int(10) unsigned DEFAULT NULL,name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,PRIMARY KEY (id),KEY id_program (program_id),KEY id_institution (institution_id),CONSTRAINT course_ibfk_1 FOREIGN KEY (institution_id) REFERENCES institution (id),
        CONSTRAINT course_ibfk_2 FOREIGN KEY (program_id) REFERENCES program (id)) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dependent_example (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_dependent_example (id INT UNSIGNED AUTO_INCREMENT NOT NULL, dependent_example_id INT UNSIGNED DEFAULT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX dependent_example_id (dependent_example_id), PRIMARY KEY(id), CONSTRAINT `sub_dependent_example_ibfk_1` FOREIGN KEY (`dependent_example_id`) REFERENCES `dependent_example` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE division (id INT UNSIGNED AUTO_INCREMENT NOT NULL, portuguese VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, english VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, spanish VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, initials VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE example (id INT UNSIGNED AUTO_INCREMENT NOT NULL, sub_dependent_example_id INT UNSIGNED DEFAULT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX sub_dependent_example_id (sub_dependent_example_id), PRIMARY KEY(id), CONSTRAINT `example_ibfk_1` FOREIGN KEY (`sub_dependent_example_id`) REFERENCES `sub_dependent_example` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE theme (id INT UNSIGNED AUTO_INCREMENT NOT NULL, initials VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, portuguese TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, english TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, spanish TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description_portuguese LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description_english LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description_spanish LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ordination INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE keyword (id INT UNSIGNED AUTO_INCREMENT NOT NULL, division_id INT UNSIGNED DEFAULT NULL, theme_id INT UNSIGNED DEFAULT NULL, portuguese VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, english VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, spanish VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_key_divi_idx (division_id), INDEX fk_key_them_idx (theme_id), PRIMARY KEY(id), CONSTRAINT `keyword_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`),
        CONSTRAINT `keyword_ibfk_2` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE method (id INT UNSIGNED AUTO_INCREMENT NOT NULL, portuguese VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, english VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, spanish VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE theory (id INT UNSIGNED AUTO_INCREMENT NOT NULL, portuguese VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, english VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, spanish VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, city_id INT UNSIGNED DEFAULT NULL, identifier VARCHAR(15) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, nickname VARCHAR(45) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, email VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, birthday DATE DEFAULT NULL, password VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, zipcode VARCHAR(9) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, street VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, number INT DEFAULT NULL, complement VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, neighborhood VARCHAR(15) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, phone INT DEFAULT NULL, cellphone INT DEFAULT NULL, roles LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, portuguese TINYINT(1) DEFAULT NULL, english TINYINT(1) DEFAULT NULL, spanish TINYINT(1) DEFAULT NULL, newsletter_associated TINYINT(1) DEFAULT NULL, newsletter_events TINYINT(1) DEFAULT NULL, newsletter_partners TINYINT(1) DEFAULT NULL, record_type TINYINT(1) DEFAULT NULL COMMENT \'perfil em código\', brazilian TINYINT(1) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\', INDEX fk_user_city_idx (city_id), PRIMARY KEY(id),
        CONSTRAINT `user_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_institution (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id BIGINT UNSIGNED DEFAULT NULL, institution_id INT UNSIGNED DEFAULT NULL, link DATE NOT NULL, unlink DATE NOT NULL, INDEX id_institution (institution_id), INDEX id_user (user_id), PRIMARY KEY(id),   CONSTRAINT `user_institution_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
        CONSTRAINT `user_institution_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_methods (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id BIGINT UNSIGNED DEFAULT NULL, method_id INT UNSIGNED DEFAULT NULL, INDEX fk_um_meth_idx (method_id), INDEX id_user (user_id), PRIMARY KEY(id), 
        CONSTRAINT `user_methods_ibfk_2` FOREIGN KEY (`method_id`) REFERENCES `method` (`id`),
        CONSTRAINT `user_methods_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_theme_keyword (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id BIGINT UNSIGNED DEFAULT NULL, keyword_id INT UNSIGNED DEFAULT NULL, INDEX fk_utk_keyw_idx (keyword_id), INDEX id_user (user_id), PRIMARY KEY(id),   CONSTRAINT `user_theme_keyword_fk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
        CONSTRAINT `user_theme_keyword_fk_4` FOREIGN KEY (`keyword_id`) REFERENCES `keyword` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_theories (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id BIGINT UNSIGNED DEFAULT NULL, theory_id INT UNSIGNED DEFAULT NULL, INDEX id_theory (theory_id), INDEX id_user (user_id), PRIMARY KEY(id), CONSTRAINT `user_theory_fk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
        CONSTRAINT `user_theory_fk_4` FOREIGN KEY (`theory_id`) REFERENCES `theory` (`id`)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE user_academics (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,user_id bigint(20) unsigned DEFAULT NULL,level smallint(5) DEFAULT NULL,status smallint(5) DEFAULT NULL,area varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,institution_id int(10) unsigned NOT NULL,program_id int(10) unsigned NOT NULL,
        other_program varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,start_date date DEFAULT NULL,end_date date DEFAULT NULL,PRIMARY KEY (id),KEY id_user (user_id),KEY institution_id (institution_id),KEY program_id (program_id),
        CONSTRAINT user_academics_ibfk_3 FOREIGN KEY (user_id) REFERENCES user (id),
        CONSTRAINT user_academics_ibfk_4 FOREIGN KEY (institution_id) REFERENCES institution (id),
        CONSTRAINT user_academics_ibfk_5 FOREIGN KEY (program_id) REFERENCES program (id)
        ) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_institutions_programs (id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
          state_first_id int(10) unsigned DEFAULT NULL,state_second_id int(10) unsigned DEFAULT NULL,institution_first_id int(10) unsigned DEFAULT NULL,institution_second_id int(10) unsigned DEFAULT NULL,program_first_id int(10) unsigned DEFAULT NULL,
          program_second_id int(10) unsigned DEFAULT NULL,user_id BIGINT(20) unsigned DEFAULT NULL,link date DEFAULT NULL,unlink date DEFAULT NULL,PRIMARY KEY (id),
          KEY user_id (user_id),
          KEY institution_first_id (institution_first_id),
          KEY institution_second_id (institution_second_id),
          KEY program_first_id (program_first_id),
          KEY state_first_id (state_first_id),
          KEY state_second_id (state_second_id),
          KEY program_second_id (program_second_id),
          CONSTRAINT FK_5D45554A69789E6A FOREIGN KEY (program_first_id) REFERENCES program (id),
          CONSTRAINT FK_5D45554AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id),
          CONSTRAINT FK_5D45554AA9A4EA13 FOREIGN KEY (program_second_id) REFERENCES program (id),
          CONSTRAINT FK_5D45554AB71BDEE1 FOREIGN KEY (state_second_id) REFERENCES state (id),
          CONSTRAINT FK_5D45554AD7BE04F8 FOREIGN KEY (state_first_id) REFERENCES state (id),
          CONSTRAINT FK_5D45554AE83FC083 FOREIGN KEY (institution_second_id) REFERENCES institution (id),
          CONSTRAINT FK_5D45554AF6286EC1 FOREIGN KEY (institution_first_id) REFERENCES institution (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_articles (id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,division_first_id int(10) unsigned DEFAULT NULL,division_second_id int(10) unsigned DEFAULT NULL,keyword_first_id int(10) unsigned DEFAULT NULL,keyword_second_id int(10) unsigned DEFAULT NULL,
        keyword_three_id int(10) unsigned DEFAULT NULL,keyword_four_id int(10) unsigned DEFAULT NULL,keyword_five_id int(10) unsigned DEFAULT NULL,keyword_six_id int(10) unsigned DEFAULT NULL,theme_first_id int(10) unsigned DEFAULT NULL,theme_second_id int(10) unsigned DEFAULT NULL,user_id BIGINT(20) unsigned DEFAULT NULL,
        portuguese tinyint(1) NOT NULL,english tinyint(1) NOT NULL,spanish tinyint(1) NOT NULL,PRIMARY KEY (id),UNIQUE KEY UNIQ_5F50D568A76ED395 (user_id),
        KEY user_id (user_id),
        KEY division_first_id (division_first_id),
        KEY division_second_id (division_second_id),
        KEY keyword_first_id (keyword_first_id),
        KEY keyword_second_id (keyword_second_id),
        KEY keyword_three_id (keyword_three_id),
        KEY keyword_four_id (keyword_four_id),
        KEY keyword_five_id (keyword_five_id),
        KEY keyword_six_id (keyword_six_id),
        KEY theme_first_id (theme_first_id),
        KEY theme_second_id (theme_second_id),
        CONSTRAINT FK_5F50D5683155C141 FOREIGN KEY (theme_second_id) REFERENCES theme (id),
        CONSTRAINT FK_5F50D568371C5171 FOREIGN KEY (division_second_id) REFERENCES division (id),
        CONSTRAINT FK_5F50D5683EE225E8 FOREIGN KEY (keyword_six_id) REFERENCES keyword (id),
        CONSTRAINT FK_5F50D5684A737028 FOREIGN KEY (theme_first_id) REFERENCES theme (id),
        CONSTRAINT FK_5F50D5687926AF0E FOREIGN KEY (keyword_first_id) REFERENCES keyword (id),
        CONSTRAINT FK_5F50D568A76ED395 FOREIGN KEY (user_id) REFERENCES user (id),
        CONSTRAINT FK_5F50D568B5565F6D FOREIGN KEY (division_first_id) REFERENCES division (id),
        CONSTRAINT FK_5F50D568B63A3001 FOREIGN KEY (keyword_five_id) REFERENCES keyword (id),
        CONSTRAINT FK_5F50D568E36B1163 FOREIGN KEY (keyword_second_id) REFERENCES keyword (id),
        CONSTRAINT FK_5F50D568E6CAC034 FOREIGN KEY (keyword_three_id) REFERENCES keyword (id),
        CONSTRAINT FK_5F50D568EA0DC6EA FOREIGN KEY (keyword_four_id) REFERENCES keyword (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE city');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE country');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE course');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE dependent_example');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_academics');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_articles');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_institutions_programs');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE division');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE example');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE institution');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE keyword');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE method');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE program');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE state');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_dependent_example');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE theme');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE theory');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_academics');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_methods');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_theme_keyword');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_theory');
    }
}

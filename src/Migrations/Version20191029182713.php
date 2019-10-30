<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191029182713 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE publisher_blacklist (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
            campaign_id INT UNSIGNED NOT NULL, 
            publisher_id INT UNSIGNED NOT NULL, 
            INDEX IDX_C3E2A395F639F774 (campaign_id), 
            INDEX IDX_C3E2A39540C86FCE (publisher_id), 
            UNIQUE INDEX publisher_blacklist_unique (campaign_id, publisher_id), 
            PRIMARY KEY(id)) 
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('CREATE TABLE event (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
            campaign_id INT UNSIGNED NOT NULL, 
            publisher_id INT UNSIGNED NOT NULL, 
            event_type VARCHAR(50) NOT NULL, 
            INDEX IDX_3BAE0AA7F639F774 (campaign_id), 
            INDEX IDX_3BAE0AA740C86FCE (publisher_id), 
            PRIMARY KEY(id)) 
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('CREATE TABLE campaign (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
            threshold INT NOT NULL, 
            source_event_type VARCHAR(50) NOT NULL, 
            measured_event_type VARCHAR(50) NOT NULL, 
            ratio_threshold DOUBLE PRECISION NOT NULL, 
            PRIMARY KEY(id)) 
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('CREATE TABLE publisher (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)) 
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('ALTER TABLE publisher_blacklist ADD CONSTRAINT FK_C3E2A395F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE publisher_blacklist ADD CONSTRAINT FK_C3E2A39540C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA740C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE publisher_blacklist DROP FOREIGN KEY FK_C3E2A395F639F774');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7F639F774');
        $this->addSql('ALTER TABLE publisher_blacklist DROP FOREIGN KEY FK_C3E2A39540C86FCE');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA740C86FCE');
        $this->addSql('DROP TABLE publisher_blacklist');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE publisher');
    }
}

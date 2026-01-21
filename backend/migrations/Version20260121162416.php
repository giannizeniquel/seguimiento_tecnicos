<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121162416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activities (id CHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, scheduled_start DATETIME NOT NULL, scheduled_end DATETIME DEFAULT NULL, actual_start DATETIME DEFAULT NULL, actual_end DATETIME DEFAULT NULL, priority VARCHAR(20) NOT NULL, location_address VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by CHAR(36) DEFAULT NULL, assigned_to CHAR(36) DEFAULT NULL, INDEX IDX_B5F1AFE5DE12AB56 (created_by), INDEX IDX_B5F1AFE589EEAF91 (assigned_to), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE activity_logs (id CHAR(36) NOT NULL, action VARCHAR(50) NOT NULL, old_value JSON DEFAULT NULL, new_value JSON DEFAULT NULL, created_at DATETIME NOT NULL, activity_id CHAR(36) DEFAULT NULL, user_id CHAR(36) DEFAULT NULL, INDEX IDX_F34B1DCE81C06096 (activity_id), INDEX IDX_F34B1DCEA76ED395 (user_id), INDEX IDX_F34B1DCE8B8E8428 (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE assignments (id CHAR(36) NOT NULL, assigned_at DATETIME NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, activity_id CHAR(36) DEFAULT NULL, technician_id CHAR(36) DEFAULT NULL, assigned_by CHAR(36) DEFAULT NULL, INDEX IDX_308A50DD81C06096 (activity_id), INDEX IDX_308A50DDE6C5D496 (technician_id), INDEX IDX_308A50DD61A2AF17 (assigned_by), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notifications (id CHAR(36) NOT NULL, type VARCHAR(20) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT NOT NULL, sent_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, user_id CHAR(36) DEFAULT NULL, activity_id CHAR(36) DEFAULT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), INDEX IDX_6000B0D381C06096 (activity_id), INDEX IDX_6000B0D3A76ED395DA46F46 (user_id, is_read), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE589EEAF91 FOREIGN KEY (assigned_to) REFERENCES users (id)');
        $this->addSql('ALTER TABLE activity_logs ADD CONSTRAINT FK_F34B1DCE81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE activity_logs ADD CONSTRAINT FK_F34B1DCEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT FK_308A50DD81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT FK_308A50DDE6C5D496 FOREIGN KEY (technician_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT FK_308A50DD61A2AF17 FOREIGN KEY (assigned_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D381C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5DE12AB56');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE589EEAF91');
        $this->addSql('ALTER TABLE activity_logs DROP FOREIGN KEY FK_F34B1DCE81C06096');
        $this->addSql('ALTER TABLE activity_logs DROP FOREIGN KEY FK_F34B1DCEA76ED395');
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY FK_308A50DD81C06096');
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY FK_308A50DDE6C5D496');
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY FK_308A50DD61A2AF17');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D381C06096');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE activity_logs');
        $this->addSql('DROP TABLE assignments');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE users');
    }
}

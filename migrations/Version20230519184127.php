<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519184127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE handler (
          id INT AUTO_INCREMENT NOT NULL,
          standard_id INT NOT NULL,
          slug VARCHAR(25) NOT NULL,
          rules JSON NOT NULL,
          INDEX IDX_939715CD6F9BFC42 (standard_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (
          id INT AUTO_INCREMENT NOT NULL,
          session_id INT NOT NULL,
          type VARCHAR(255) NOT NULL,
          parameters JSON NOT NULL,
          text_message VARCHAR(255) NOT NULL,
          from_identifier VARCHAR(50) NOT NULL,
          to_identifier VARCHAR(50) NOT NULL,
          message_identifier VARCHAR(50) DEFAULT NULL,
          created_at DATETIME DEFAULT NULL,
          updated_at DATETIME DEFAULT NULL,
          INDEX IDX_B6BD307F613FECDF (session_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (
          id INT AUTO_INCREMENT NOT NULL,
          status SMALLINT NOT NULL,
          session_id VARCHAR(20) NOT NULL,
          initiator_identifier VARCHAR(50) NOT NULL,
          receiver_identifier VARCHAR(50) NOT NULL,
          created_at DATETIME DEFAULT NULL,
          updated_at DATETIME DEFAULT NULL,
          UNIQUE INDEX UNIQ_D044D5D4613FECDF (session_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE standard (
          id INT AUTO_INCREMENT NOT NULL,
          default_handler_id INT DEFAULT NULL,
          slug VARCHAR(25) NOT NULL,
          meta_data JSON NOT NULL,
          UNIQUE INDEX UNIQ_10F7D787EECB7420 (default_handler_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE
          handler
        ADD
          CONSTRAINT FK_939715CD6F9BFC42 FOREIGN KEY (standard_id) REFERENCES standard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE
          message
        ADD
          CONSTRAINT FK_B6BD307F613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE
          standard
        ADD
          CONSTRAINT FK_10F7D787EECB7420 FOREIGN KEY (default_handler_id) REFERENCES handler (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE handler DROP FOREIGN KEY FK_939715CD6F9BFC42');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F613FECDF');
        $this->addSql('ALTER TABLE standard DROP FOREIGN KEY FK_10F7D787EECB7420');
        $this->addSql('DROP TABLE handler');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE standard');
    }
}

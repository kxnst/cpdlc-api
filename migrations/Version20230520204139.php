<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520204139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD standard_id INT NOT NULL');
        $this->addSql('ALTER TABLE
          session
        ADD
          CONSTRAINT FK_D044D5D46F9BFC42 FOREIGN KEY (standard_id) REFERENCES standard (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D044D5D46F9BFC42 ON session (standard_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D46F9BFC42');
        $this->addSql('DROP INDEX IDX_D044D5D46F9BFC42 ON session');
        $this->addSql('ALTER TABLE session DROP standard_id');
    }
}

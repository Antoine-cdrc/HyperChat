<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511155720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ is_read à la table chat';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat ADD is_read TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat DROP is_read');
    }
}

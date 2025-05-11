<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510114718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD sender_id INT NOT NULL, ADD receiver_id INT NOT NULL, ADD message LONGTEXT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD twilio_channel_sid VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat ADD CONSTRAINT FK_659DF2AACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659DF2AAF624B39D ON chat (sender_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659DF2AACD53EDB6 ON chat (receiver_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AACD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_659DF2AAF624B39D ON chat
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_659DF2AACD53EDB6 ON chat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat DROP sender_id, DROP receiver_id, DROP message, DROP created_at, DROP twilio_channel_sid
        SQL);
    }
}

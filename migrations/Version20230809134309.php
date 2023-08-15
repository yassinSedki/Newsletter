<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809134309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_tracking CHANGE tracked_id tracked_id VARCHAR(255) NOT NULL, CHANGE viewed viewed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user DROP email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_tracking CHANGE tracked_id tracked_id INT NOT NULL, CHANGE viewed viewed TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD email VARCHAR(255) NOT NULL');
    }
}

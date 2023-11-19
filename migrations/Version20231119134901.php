<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119134901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds symfony source';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO sources (id, name) VALUES ('eefdac30-454b-4455-99e8-cfe3a2bb6111','symfony.com')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM sources WHERE id='eefdac30-454b-4455-99e8-cfe3a2bb6111'");
    }
}

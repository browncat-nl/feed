<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723091530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds stitcher source';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO sources (id, name) VALUES ('0c65cd65-bf4e-4bd1-ac1d-ccf707e19361','stitcher.io')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM sources WHERE id='0c65cd65-bf4e-4bd1-ac1d-ccf707e19361'");
    }
}

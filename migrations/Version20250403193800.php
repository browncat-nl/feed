<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403193800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ALTER source_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sources ALTER feed_url TYPE VARCHAR(512)');
        $this->addSql('ALTER TABLE sources ALTER feed_url DROP DEFAULT');
        $this->addSql('ALTER TABLE sources ALTER category_id DROP DEFAULT');
        $this->addSql('ALTER INDEX idx_d25d65f2c6b58e54 RENAME TO IDX_D25D65F212469DE2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP CONSTRAINT FK_BFDD3168953C1C61');
        $this->addSql('ALTER TABLE articles ALTER source_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE sources ALTER feed_url TYPE VARCHAR(512)');
        $this->addSql('ALTER TABLE sources ALTER feed_url SET DEFAULT \'\'');
        $this->addSql('ALTER TABLE sources ALTER category_id SET DEFAULT \'fc4b87d7-9b19-4770-b312-3ccb1da69e54\'');
        $this->addSql('ALTER INDEX idx_d25d65f212469de2 RENAME TO idx_d25d65f2c6b58e54');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124212701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create categories table and assign the "default" category to each existing source.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE categories (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX category_name_index ON categories (name)');

        // Create default category
        $this->addSql("INSERT INTO categories (id, name) VALUES ('fc4b87d7-9b19-4770-b312-3ccb1da69e54', 'default')");

        $this->addSql("ALTER TABLE sources ADD category_id VARCHAR DEFAULT 'fc4b87d7-9b19-4770-b312-3ccb1da69e54'");
        $this->addSql('ALTER TABLE sources ADD CONSTRAINT FK_D25D65F2C6B58E54 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D25D65F2C6B58E54 ON sources (category_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE categories CASCADE');
        $this->addSql('ALTER TABLE sources DROP CONSTRAINT FK_D25D65F2C6B58E54');
        $this->addSql('DROP INDEX IDX_D25D65F2C6B58E54');
        $this->addSql('ALTER TABLE sources DROP category_id');
    }
}

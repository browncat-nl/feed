<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230702095908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change `url` on `articles` from TEXT to VARCHAR and index the column.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ALTER url TYPE VARCHAR(512)');
        $this->addSql('CREATE INDEX url_index ON articles (url)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX url_index');
        $this->addSql('ALTER TABLE articles ALTER url TYPE TEXT');
    }
}

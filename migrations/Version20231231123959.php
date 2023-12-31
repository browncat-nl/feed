<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231231123959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Delete 'php.watch' source
        $this->addSql("DELETE FROM sources WHERE id='9930338f-d07c-4030-ade6-5ac19821e554'");

        $this->addSql("INSERT INTO sources (id, name) VALUES ('a9ba164c-5a86-43d2-b024-f38e6a227a41', 'php.watch-news')");
        $this->addSql("INSERT INTO sources (id, name) VALUES ('18929b96-79a4-44bf-8d97-f442851b7b44', 'php.watch-changes')");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("INSERT INTO sources (id, name) VALUES ('9930338f-d07c-4030-ade6-5ac19821e554','php.watch')");

        $this->addSql("DELETE FROM sources WHERE id='a9ba164c-5a86-43d2-b024-f38e6a227a41'");
        $this->addSql("DELETE FROM sources WHERE id='18929b96-79a4-44bf-8d97-f442851b7b44'");
    }
}

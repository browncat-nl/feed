<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122082702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds php.watch source';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO sources (id, name) VALUES ('9930338f-d07c-4030-ade6-5ac19821e554','php.watch')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM sources WHERE id='9930338f-d07c-4030-ade6-5ac19821e554'");
    }
}

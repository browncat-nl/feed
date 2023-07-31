<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230731184324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds martinfowler source';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO sources (id, name) VALUES ('f2f38859-ba40-4252-90c5-a956056a1dae','martinfowler.com')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM sources WHERE id='f2f38859-ba40-4252-90c5-a956056a1dae'");
    }
}

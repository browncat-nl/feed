<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229101126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the feed url to the source';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE sources ADD feed_url VARCHAR(512) NOT NULL DEFAULT ''");

        $this->addSql("UPDATE sources SET feed_url='https://martinfowler.com/feed.atom' WHERE name='martinfowler.com'");
        $this->addSql("UPDATE sources SET feed_url='https://php.watch/feed/php-changes.xml' WHERE name='php.watch-changes'");
        $this->addSql("UPDATE sources SET feed_url='https://php.watch/feed/news.xml' WHERE name='php.watch-news'");
        $this->addSql("UPDATE sources SET feed_url='https://stitcher.io/rss' WHERE name='stitcher.io'");
        $this->addSql("UPDATE sources SET feed_url='https://feeds.feedburner.com/symfony/blog' WHERE name='symfony.com'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sources DROP feed_url');
    }
}

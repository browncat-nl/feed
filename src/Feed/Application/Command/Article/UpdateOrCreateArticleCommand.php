<?php

namespace App\Feed\Application\Command\Article;

use App\Feed\Application\Command\Article\Handler\UpdateOrCreateArticleHandler;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * @see UpdateOrCreateArticleHandler
 */
final readonly class UpdateOrCreateArticleCommand
{
    public function __construct(
        public string $title,
        public string $summary,
        public string $url,
        public DateTime $updated,
        public string $sourceName,
    ) {
    }
}

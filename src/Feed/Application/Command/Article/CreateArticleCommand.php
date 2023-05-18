<?php

namespace App\Feed\Application\Command\Article;

use App\Feed\Application\Command\Article\Handler\CreateArticleHandler;
use DateTime;

/**
 * @see CreateArticleHandler
 */
final readonly class CreateArticleCommand
{
    public function __construct(
        public string $articleId,
        public string $title,
        public string $summary,
        public string $url,
        public DateTime $updated,
        public string $sourceId,
    ) {
    }
}

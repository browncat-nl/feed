<?php

namespace App\Feed\Application\Command;

use App\Feed\Application\Command\Handler\CreateArticleHandler;
use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
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

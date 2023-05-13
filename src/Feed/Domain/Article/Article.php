<?php

namespace App\Feed\Domain\Article;

use App\Feed\Domain\Article\Url\Url;
use App\Feed\Domain\Source\Source;
use DateTime;

final readonly class Article
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public string $summary,
        public Url $url,
        public DateTime $updated,
        public Source $source,
    ) {
    }
}

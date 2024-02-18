<?php

namespace App\Feed\Domain\Article\Exception;

use App\Feed\Domain\Article\ArticleId;

final class ArticleNotFoundException extends \Exception
{
    public static function withArticleId(ArticleId $articleId): self
    {
        return new self(sprintf('Article with id %s not found.', (string) $articleId));
    }
}

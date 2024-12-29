<?php

namespace App\Feed\Application\Command\Article\Handler;

use App\Common\Domain\Url\Exception\MalformedUrlException;
use App\Common\Domain\Url\Url;
use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Feed\Application\Command\Article\UpsertArticleCommand;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\SourceRepository;
use Ramsey\Uuid\Uuid;

/**
 * Updates the article if the url is found in the article repository, otherwise it creates a new article.
 */
#[AsCommandHandler]
final readonly class UpsertArticleHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private SourceRepository $sourceRepository,
    ) {
    }

    /**
     * @throws MalformedUrlException
     * @throws SourceNotFoundException
     */
    public function __invoke(UpsertArticleCommand $command): void
    {
        $source = $this->sourceRepository->findByNameOrThrow($command->sourceName);

        $articleUrl = Url::createFromString($command->url);

        $existingArticle = $this->articleRepository->findByUrl($articleUrl);

        if ($existingArticle === null) {
            $article = new Article(
                new ArticleId(Uuid::uuid4()),
                $command->title,
                $command->summary,
                Url::createFromString($command->url),
                $command->updated,
                $source,
            );

            $this->articleRepository->save($article);

            return;
        }

        $existingArticle->updateArticle(
            $command->title,
            $command->summary,
            Url::createFromString($command->url),
            $command->updated,
            $source
        );

        $this->articleRepository->save($existingArticle);
    }
}

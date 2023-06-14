<?php

namespace App\Feed\Application\Command\Article\Handler;

use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Feed\Application\Command\Article\CreateArticleCommand;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;
use App\Feed\Domain\Article\Url\Exception\MalformedUrlException;
use App\Feed\Domain\Article\Url\Url;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsCommandHandler]
final readonly class CreateArticleHandler
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
    public function __invoke(CreateArticleCommand $command): void
    {
        $articleId = new ArticleId($command->articleId);
        $sourceId = new SourceId($command->sourceId);

        $source = $this->sourceRepository->findOrThrow($sourceId);

        $article = new Article(
            $articleId,
            $command->title,
            $command->summary,
            Url::createFromString($command->url),
            $command->updated,
            $source,
        );

        $this->articleRepository->save($article);
    }
}

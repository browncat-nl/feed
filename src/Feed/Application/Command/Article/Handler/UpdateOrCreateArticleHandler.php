<?php

namespace App\Feed\Application\Command\Article\Handler;

use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Feed\Application\Command\Article\UpdateOrCreateArticleCommand;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;
use App\Feed\Domain\Article\Url\Exception\MalformedUrlException;
use App\Feed\Domain\Article\Url\Url;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Updates the article if the url is found in the article repository, otherwise it creates a new article.
 */
#[AsCommandHandler]
final readonly class UpdateOrCreateArticleHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private SourceRepository $sourceRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws MalformedUrlException
     * @throws SourceNotFoundException
     */
    public function __invoke(UpdateOrCreateArticleCommand $command): void
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

        if ($existingArticle->getUpdated() === $command->updated) {
            return;
        }

        if ($existingArticle->getUpdated() > $command->updated) {
            $this->logger->warning('Existing article is fresher then the newly given one.', [
                'articleId' => $existingArticle->getId(),
                'existingArticleUpdated' => $existingArticle->getUpdated(),
                'newArticleUpdated' => $existingArticle->getUpdated(),
                'url' => $command->url,
            ]);

            return;
        }

        $existingArticle->setTitle($command->title);
        $existingArticle->setSummary($command->summary);
        $existingArticle->setUrl(Url::createFromString($command->url));
        $existingArticle->setUpdated($command->updated);
        $existingArticle->setSource($source);

        $this->articleRepository->save($existingArticle);
    }
}

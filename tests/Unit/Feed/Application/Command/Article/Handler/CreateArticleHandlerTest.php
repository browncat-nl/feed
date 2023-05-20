<?php

namespace Unit\Feed\Application\Command\Article\Handler;

use App\Feed\Application\Command\Article\CreateArticleCommand;
use App\Feed\Application\Command\Article\Handler\CreateArticleHandler;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\Url\Exception\MalformedUrlException;
use App\Feed\Domain\Article\Url\Exception\SchemeNotSupportedException;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\SourceId;
use DateTime;
use Dev\Feed\Factory\SourceFactory;
use Dev\Feed\Repository\InMemoryArticleRepository;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CreateArticleHandlerTest extends TestCase
{
    private CreateArticleHandler $handler;
    private InMemoryArticleRepository $articleRepository;
    private InMemorySourceRepository $sourceRepository;

    public function setUp(): void
    {
        $this->articleRepository = new InMemoryArticleRepository();
        $this->sourceRepository = new InMemorySourceRepository();

        $this->handler = new CreateArticleHandler(
            $this->articleRepository,
            $this->sourceRepository,
        );
    }

    /**
     * @test
     */
    public function it_should_create_an_article(): void
    {
        // Arrange
        $source = (new SourceFactory())->create();
        $articleId = Uuid::uuid4();

        $this->sourceRepository->save($source);

        $title = 'Very interesting article';
        $summary = 'This article is about bananas.';
        $url = 'https://example.com/bananas?tasty=yes&tracking=loads#banana-phone';
        $updated = new DateTime();

        $command = new CreateArticleCommand(
            $articleId,
            $title,
            $summary,
            $url,
            $updated,
            $source->getId(),
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        $article = $this->articleRepository->find(new ArticleId($articleId));

        self::assertNotNull($article);
        self::assertSame((string) $articleId, (string) $article->getId());
        self::assertSame($title, $article->getTitle());
        self::assertSame($summary, $article->getSummary());
        self::assertSame($url, (string) $article->getUrl());
        self::assertSame($updated, $article->getUpdated());
        self::assertSame($source, $article->getSource());
    }

    /**
     * @test
     */
    public function it_should_throw_if_source_does_not_exist(): void
    {
        // Arrange
        $sourceId = Uuid::uuid4();
        $articleId = Uuid::uuid4();

        $command = new CreateArticleCommand(
            $articleId,
            'Very interesting article',
            'This article is about bananas.',
            'https://example.com/bananas?tasty=yes&tracking=loads#banana-phone',
            new DateTime(),
            $sourceId,
        );

        // Assert
        self::expectExceptionObject(SourceNotFoundException::withSourceId(new SourceId($sourceId)));

        // Act
        $this->handler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_should_throw_if_url_is_malformed(): void
    {
        // Arrange
        $source = (new SourceFactory())->create();
        $articleId = Uuid::uuid4();

        $this->sourceRepository->save($source);

        $malformedUrl = 'missing-scheme.com';

        $command = new CreateArticleCommand(
            $articleId,
            'Very interesting article',
            'This article is about bananas.',
            $malformedUrl,
            new DateTime(),
            $source->getId(),
        );

        // Assert
        self::expectExceptionObject(MalformedUrlException::withUrl($malformedUrl));

        // Act
        $this->handler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_should_throw_if_url_scheme_is_not_supported(): void
    {
        // Arrange
        $source = (new SourceFactory())->create();
        $articleId = Uuid::uuid4();

        $this->sourceRepository->save($source);

        $command = new CreateArticleCommand(
            $articleId,
            'Very interesting article',
            'This article is about bananas.',
            'ftp://example.com/ftp-is-an-unsupported-scheme',
            new DateTime(),
            $source->getId(),
        );

        // Assert
        self::expectExceptionObject(SchemeNotSupportedException::withScheme('ftp'));

        // Act
        $this->handler->__invoke($command);
    }
}

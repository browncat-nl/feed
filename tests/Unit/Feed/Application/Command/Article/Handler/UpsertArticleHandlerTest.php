<?php

namespace Unit\Feed\Application\Command\Article\Handler;

use App\Common\Domain\Url\Exception\MalformedUrlException;
use App\Common\Domain\Url\Exception\SchemeNotSupportedException;
use App\Feed\Application\Command\Article\Handler\UpsertArticleHandler;
use App\Feed\Application\Command\Article\UpsertArticleCommand;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use DateTime;
use Dev\Feed\Factory\ArticleFactory;
use Dev\Feed\Factory\SourceFactory;
use Dev\Feed\Repository\InMemoryArticleRepository;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class UpsertArticleHandlerTest extends TestCase
{
    private UpsertArticleHandler $handler;
    private InMemoryArticleRepository $articleRepository;
    private InMemorySourceRepository $sourceRepository;

    public function setUp(): void
    {
        $this->articleRepository = new InMemoryArticleRepository();
        $this->sourceRepository = new InMemorySourceRepository();

        $this->handler = new UpsertArticleHandler(
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
        $source = SourceFactory::setup()->create();

        $this->sourceRepository->save($source);

        $title = 'Very interesting article';
        $summary = 'This article is about bananas.';
        $url = 'https://example.com/bananas?tasty=yes&tracking=loads#banana-phone';
        $updated = new DateTime();

        $command = new UpsertArticleCommand(
            $title,
            $summary,
            $url,
            $updated,
            $source->getName(),
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        $article = $this->articleRepository->findByUrl($url);

        self::assertNotNull($article);
        self::assertInstanceOf(ArticleId::class, $article->getId());
        self::assertSame($title, $article->getTitle());
        self::assertSame($summary, $article->getSummary());
        self::assertSame($url, (string) $article->getUrl());
        self::assertSame($updated, $article->getUpdated());
        self::assertSame($source, $article->getSource());
    }

    /**
     * @test
     */
    public function it_should_update_the_article(): void
    {
        // Arrange
        $source = SourceFactory::setup()->create();
        $existingArticle = ArticleFactory::setup()->withUpdated(new DateTime('1996-10-27 00:00:00'))->create();

        $this->sourceRepository->save($source);
        $this->articleRepository->save($existingArticle);

        $title = 'Very interesting article';
        $summary = 'This article is about bananas.';
        $updated = new DateTime('2022-10-5 00:00:00');

        $command = new UpsertArticleCommand(
            $title,
            $summary,
            (string) $existingArticle->getUrl(),
            $updated,
            $source->getName(),
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        $article = $this->articleRepository->find($existingArticle->getId());

        self::assertNotNull($article);
        self::assertSame($title, $article->getTitle());
        self::assertSame($summary, $article->getSummary());
        self::assertEquals($existingArticle->getUrl(), $article->getUrl());
        self::assertSame($updated, $article->getUpdated());
        self::assertSame($source, $article->getSource());
    }

    /**
     * @test
     */
    public function it_should_throw_if_source_does_not_exist(): void
    {
        // Arrange
        $command = new UpsertArticleCommand(
            'Very interesting article',
            'This article is about bananas.',
            'https://example.com/bananas?tasty=yes&tracking=loads#banana-phone',
            new DateTime(),
            'non-existing-source',
        );

        // Assert
        self::expectExceptionObject(SourceNotFoundException::withName('non-existing-source'));

        // Act
        $this->handler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_should_throw_if_url_is_malformed(): void
    {
        // Arrange
        $source = SourceFactory::setup()->create();
        $articleId = Uuid::uuid4();

        $this->sourceRepository->save($source);

        $malformedUrl = 'missing-scheme.com';

        $command = new UpsertArticleCommand(
            'Very interesting article',
            'This article is about bananas.',
            $malformedUrl,
            new DateTime(),
            $source->getName(),
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
        $source = SourceFactory::setup()->create();
        $articleId = Uuid::uuid4();

        $this->sourceRepository->save($source);

        $command = new UpsertArticleCommand(
            'Very interesting article',
            'This article is about bananas.',
            'ftp://example.com/ftp-is-an-unsupported-scheme',
            new DateTime(),
            $source->getName(),
        );

        // Assert
        self::expectExceptionObject(SchemeNotSupportedException::withScheme('ftp'));

        // Act
        $this->handler->__invoke($command);
    }
}

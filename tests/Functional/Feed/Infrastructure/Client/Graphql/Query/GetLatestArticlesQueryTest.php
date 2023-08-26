<?php

namespace Functional\Feed\Infrastructure\Client\Graphql\Query;

use Dev\Feed\Factory\ArticleFactory;
use Dev\Testing\PHPUnit\GraphqlTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetLatestArticlesQueryTest extends GraphqlTestCase
{
    /**
     * @test
     */
    public function it_should_return_an_empty_list_if_there_are_no_articles(): void
    {
        // Act
        $response = $this->operation(
            'test',
            <<<GRAPHQL
            query test { 
                getLatestArticles {
                    title,
                    summary,
                    url
                  }
            }
            GRAPHQL
        );

        self::assertNotFalse($response->getContent());
        $responseBody = json_decode($response->getContent(), true);

        // Assert
        self::assertSame([
            'data' => [
                'getLatestArticles' => []
            ],
        ], $responseBody);
    }

    /**
     * @test
     */
    public function it_should_return_the_latest_articles(): void
    {
        // Arrange
        $article = (new ArticleFactory())->withUpdated(new \DateTime('2023-10-10 8:00:00'))->create();
        $article2 = (new ArticleFactory())->withUpdated(new \DateTime('2012-01-5 15:30:00'))->create();
        $article3 = (new ArticleFactory())->withUpdated(new \DateTime('2012-01-5 15:30:01'))->create();

        $this->getDoctrine()->persist($article->getSource());
        $this->getDoctrine()->persist($article);

        $this->getDoctrine()->persist($article2->getSource());
        $this->getDoctrine()->persist($article2);

        $this->getDoctrine()->persist($article3->getSource());
        $this->getDoctrine()->persist($article3);

        $this->getDoctrine()->flush();

        // Act
        $response = $this->operation(
            'test',
            <<<GRAPHQL
            query test { 
                getLatestArticles {
                    title,
                    summary,
                    url
                    source {
                        id,
                        name
                    },
                    updated                
                  }
            }
            GRAPHQL
        );

        self::assertNotFalse($response->getContent());
        $responseBody = json_decode($response->getContent(), true);

        // Assert
        self::assertSame([
            'data' => [
                'getLatestArticles' => [
                    [
                        'title' => $article->getTitle(),
                        'summary' => $article->getSummary(),
                        'url' => (string) $article->getUrl(),
                        'source' => [
                            'id' => (string) $article->getSource()->getId(),
                            'name' => $article->getSource()->getName(),
                        ],
                        'updated' => $article->getUpdated()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'title' => $article3->getTitle(),
                        'summary' => $article3->getSummary(),
                        'url' => (string) $article3->getUrl(),
                        'source' => [
                            'id' => (string) $article3->getSource()->getId(),
                            'name' => $article3->getSource()->getName(),
                        ],
                        'updated' => $article3->getUpdated()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'title' => $article2->getTitle(),
                        'summary' => $article2->getSummary(),
                        'url' => (string) $article2->getUrl(),
                        'source' => [
                            'id' => (string) $article2->getSource()->getId(),
                            'name' => $article2->getSource()->getName(),
                        ],
                        'updated' => $article2->getUpdated()->format('Y-m-d H:i:s'),
                    ],
                ],
            ],
        ], $responseBody);
    }
}

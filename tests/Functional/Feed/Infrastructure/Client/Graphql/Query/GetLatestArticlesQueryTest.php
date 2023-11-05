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
            query test(\$first: Int, \$after: String) { 
                getLatestArticles(first: \$first, after: \$after) {
                    edges {
                      cursor
                      node {
                        ...on Article {
                          title,
                        }
                      }
                    },
                    pageInfo {
                      hasNextPage,
                      startCursor,
                      endCursor
                    },
                    totalCount
               }    
            }
            GRAPHQL,
            [
                'first' => 10
            ]
        );

        self::assertNotFalse($response->getContent());
        $responseBody = json_decode($response->getContent(), true);

        // Assert
        self::assertSame([
            'data' => [
                'getLatestArticles' => [
                    'edges' => [],
                    'pageInfo' => [
                        'hasNextPage' => false,
                        'startCursor' => null,
                        'endCursor' => null,
                    ],
                    'totalCount' => 0,
                ],
            ],
        ], $responseBody);
    }
    /**
     * @test
     */
    public function it_should_return_the_latest_articles_paginated(): void
    {
        // Arrange
        $article = (new ArticleFactory())->withUpdated(new \DateTime('2023-10-10 8:00:00'))->create();
        $article2 = (new ArticleFactory())->withUpdated(new \DateTime('2012-01-5 15:30:00'))->create();
        $article3 = (new ArticleFactory())->withUpdated(new \DateTime('2012-01-5 15:30:01'))->create();
        $article4 = (new ArticleFactory())->withUpdated(new \DateTime('2012-01-4 15:30:00'))->create();

        $this->getDoctrine()->persist($article->getSource());
        $this->getDoctrine()->persist($article);

        $this->getDoctrine()->persist($article2->getSource());
        $this->getDoctrine()->persist($article2);

        $this->getDoctrine()->persist($article3->getSource());
        $this->getDoctrine()->persist($article3);

        $this->getDoctrine()->persist($article4->getSource());
        $this->getDoctrine()->persist($article4);

        $this->getDoctrine()->flush();

        // Act
        $response = $this->operation(
            'test',
            <<<GRAPHQL
            query test(\$first: Int, \$after: String) { 
                getLatestArticles(first: \$first, after: \$after) {
                    edges {
                        cursor
                        node {
                            title,
                            summary,
                            url
                            source {
                                id,
                                name
                            },
                            updated                
                        }
                    },
                    pageInfo {
                      hasNextPage,
                      startCursor,
                      endCursor
                    },
                    totalCount                    
                }
            }
            GRAPHQL,
            [
                'first' => 2,
                'after' => base64_encode('arrayconnection:0'),
            ]
        );

        self::assertNotFalse($response->getContent());
        $responseBody = json_decode($response->getContent(), true);

        // Assert
        self::assertSame([
            'data' => [
                'getLatestArticles' => [
                    'edges' => [
                        [
                            'cursor' => base64_encode('arrayconnection:1'),
                            'node' => [
                                'title' => $article3->getTitle(),
                                'summary' => $article3->getSummary(),
                                'url' => (string) $article3->getUrl(),
                                'source' => [
                                    'id' => (string) $article3->getSource()->getId(),
                                    'name' => $article3->getSource()->getName(),
                                ],
                                'updated' => $article3->getUpdated()->format('Y-m-d H:i:s'),
                            ],
                        ],
                        [
                            'cursor' => base64_encode('arrayconnection:2'),
                            'node' => [
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
                    'pageInfo' => [
                        'hasNextPage' => true,
                        'startCursor' => base64_encode('arrayconnection:1'),
                        'endCursor' => base64_encode('arrayconnection:2'),
                    ],
                    'totalCount' => 4,
                ],
            ],
        ], $responseBody);
    }
}

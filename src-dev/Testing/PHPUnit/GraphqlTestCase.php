<?php

namespace Dev\Testing\PHPUnit;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

abstract class GraphqlTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createClient();
        $this->client->disableReboot();

        $this->getDoctrine()->getConnection()->setNestTransactionsWithSavepoints(true);
        $this->getDoctrine()->getConnection()->setAutoCommit(false);
        $this->getDoctrine()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->getDoctrine()->rollback();

        parent::tearDown();
    }

    public function getDoctrine(): EntityManagerInterface
    {
        $doctrine = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        Assert::isInstanceOf($doctrine, EntityManagerInterface::class);

        return $doctrine;
    }

    /**
     * @param array<string, mixed> $variables
     * @return Response
     */
    protected function operation(
        string $operationName,
        string $query,
        array $variables = [],
    ): Response {
        // Clear entity manager as to not serve cached entities in response.
        $this->getDoctrine()->clear();

        $body = [
            'operationName' => $operationName,
            'variables' => $variables,
            'query' => $query,
        ];

        $this->client->jsonRequest(
            'POST',
            '/',
            $body,
        );

        return $this->client->getResponse();
    }
}

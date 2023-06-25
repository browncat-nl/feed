<?php

namespace Dev\Testing\PHPUnit;

use App\Feed\Infrastructure\Persistence\Doctrine\Source\DoctrineSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineTestCase extends KernelTestCase
{
    private ?ManagerRegistry $doctrine = null;

    protected function getDoctrine(): ManagerRegistry
    {
        if ($this->doctrine === null) {
            throw new LogicException(
                "Calling `getDoctrine` before calling `parent::setUp()` is not possible."
            );
        }

        return $this->doctrine;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        if ($this->doctrine === null) {
            throw new LogicException(
                "Calling `getEntityManager` before calling `parent::setUp()` is not possible."
            );
        }

        $entityManager = $this->doctrine->getManager();

        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        return $entityManager;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $doctrine = $this->getContainer()->get('doctrine');

        self::assertInstanceOf(ManagerRegistry::class, $doctrine);

        $this->doctrine = $doctrine;

        $this->getEntityManager()->getConnection()->setNestTransactionsWithSavepoints(true);
        $this->getEntityManager()->getConnection()->setAutoCommit(false);
        $this->getEntityManager()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->getEntityManager()->rollback();

        parent::tearDown();
    }
}

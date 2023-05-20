<?php

namespace App\Common\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use LogicException;

/**
 * @template T of object
 */
abstract readonly class DoctrineRepository
{
    protected EntityManagerInterface $em;

    /**
     * @var class-string<T>
     */
    private string $entityName;

    /**
     * @param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $manager = $registry->getManagerForClass($entityClass);

        if ($manager === null) {
            throw new LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                $entityClass
            ));
        }

        if ($manager instanceof EntityManagerInterface === false) {
            throw new LogicException(sprintf(
                'Expected entity manager "%s" to be an instance of "%s".',
                $manager::class,
                EntityManagerInterface::class,
            ));
        }

        $classMetadata = $manager->getClassMetadata($entityClass);

        $this->em = $manager;
        $this->entityName = $classMetadata->name;
    }

    /**
     * @param T ...$objects
     */
    public function saveWithoutTransaction(object ...$objects): void
    {
        foreach ($objects as $object) {
            $this->em->persist($object);
        }

        $this->em->flush();
    }

    /**
     * @param string $id
     * @return ?T
     */
    public function findWithoutLocking(string $id): ?object
    {
        return $this->em->find($this->entityName, $id);
    }

    public function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select($alias)
            ->from($this->entityName, $alias, $indexBy);
    }
}

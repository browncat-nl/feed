<?php

namespace Dev\Common\Infrastructure\Cache;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[AsDecorator(ArrayAdapter::class)]
class RecordingCache implements CacheInterface
{
    /**
     * @var array<string, list<mixed>>
     */
    private array $cacheHits = [];

    /**
     * @var array<string, bool>
     */
    private array $deletes = [];

    public function __construct(
        #[AutowireDecorated]
        private ArrayAdapter $inner,
    ) {
    }

    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null): mixed
    {
        $isHit = true;

        $result =  $this->inner->get(
            $key,
            function (ItemInterface $item) use ($callback, &$isHit) {
                // The callback is called, this means the item is not found in the cache. The cache is not hit.
                $isHit = false;

                return $callback($item, true);
            },
            $beta,
            $metadata
        );

        if ($isHit) {
            $this->cacheHits[$key][] = $result;
        }

        return $result;
    }

    public function delete(string $key): bool
    {
        $succeeded = $this->inner->delete($key);

        $this->deletes[$key] = $succeeded;

        return $succeeded;
    }

    public function cacheIsHitForKey(string $key): bool
    {
        return isset($this->cacheHits[$key]);
    }

    public function cacheIsDeletedForKey(string $key): bool
    {
        return isset($this->deletes[$key]) && $this->deletes[$key];
    }
}

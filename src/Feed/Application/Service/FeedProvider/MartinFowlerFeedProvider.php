<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;
use App\Feed\Application\Service\FeedItemNormalizer\FeedItemNormalizer;
use App\Feed\Domain\Source\SourceRepository;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class MartinFowlerFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
        #[AutowireIterator(FeedItemNormalizer::class)]
        private iterable $feedItemNormalizers,
        private SourceRepository $sourceRepository,
    ) {
    }


    public static function getSource(): string
    {
        return 'martinfowler.com';
    }

    public function fetchFeedItems(): array
    {
        $source = $this->sourceRepository->findByNameOrThrow($this::getSource());

        $newsItems = [];

        foreach ($this->feedParser->fetchFeed($source->getName(), $source->getUrl()) as $feedItem) {
            foreach ($this->feedItemNormalizers as $feedItemNormalizer) {
                $feedItem = $feedItemNormalizer($feedItem);
            }

            $newsItems[] = $feedItem;
        }

        return $newsItems;
    }
}

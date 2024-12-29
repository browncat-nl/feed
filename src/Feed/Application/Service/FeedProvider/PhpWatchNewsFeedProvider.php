<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;
use App\Feed\Domain\Source\SourceRepository;

final class PhpWatchNewsFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
        private SourceRepository $sourceRepository,
    ) {
    }


    public static function getSource(): string
    {
        return 'php.watch-news';
    }

    public function fetchFeedItems(): array
    {
        $source = $this->sourceRepository->findByNameOrThrow($this::getSource());

        return $this->feedParser->fetchFeed($source->getName(), $source->getUrl());
    }
}

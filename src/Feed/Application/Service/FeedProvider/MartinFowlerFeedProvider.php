<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;
use App\Feed\Domain\Source\SourceRepository;

final readonly class MartinFowlerFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
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

        foreach ($this->feedParser->fetchFeed($source->getName(), $source->getUrl()) as $item) {
            $newsItems[] = new FeedItem(
                $item->title,
                $this->deriveFirstParagraphFromHtmlText($item->summary),
                $item->url,
                $item->updated,
                $item->source,
            );
        }

        return $newsItems;
    }

    private function deriveFirstParagraphFromHtmlText(string $text): string
    {
        $textFirstParagraphStart = strpos($text, '<p>');

        if ($textFirstParagraphStart === false) {
            return html_entity_decode(strip_tags(mb_strimwidth($text, 0, 277, "...")));
        }

        $textFirstParagraphEnd = strpos($text, '</p>', $textFirstParagraphStart);

        $firstParagraph = substr(
            $text,
            $textFirstParagraphStart,
            $textFirstParagraphEnd - $textFirstParagraphStart + 4
        );

        $firstParagraph = preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($firstParagraph)));

        return trim($firstParagraph ?? '');
    }
}

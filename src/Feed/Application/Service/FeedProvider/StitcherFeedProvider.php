<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;
use App\Feed\Domain\Source\SourceRepository;

final readonly class StitcherFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
        private SourceRepository $sourceRepository,
    ) {
    }

    public static function getSource(): string
    {
        return 'stitcher.io';
    }

    /**
     * @return list<FeedItem>
     */
    public function fetchFeedItems(): array
    {
        $source = $this->sourceRepository->findByNameOrThrow($this::getSource());

        $feedItems = [];

        foreach ($this->feedParser->fetchFeed($source->getName(), $source->getUrl()) as $item) {
            $feedItems[] = new FeedItem(
                trim(html_entity_decode($item->title)), // Simple pie double encodes html, let's fix this later
                $this->deriveFirstParagraphFromHtmlText($item->summary),
                $item->url,
                $item->updated,
                $item->source,
            );
        }

        return $feedItems;
    }

    private function deriveFirstParagraphFromHtmlText(string $text): string
    {
        $textFirstParagraphStart = strpos($text, '<p>');

        if ($textFirstParagraphStart === false) {
            return html_entity_decode(strip_tags(mb_strimwidth($text, 0, 277, "...")));
        }

        $textFirstParagraphEnd = strpos($text, '</p>', $textFirstParagraphStart);

        $firstParagraph =  substr(
            $text,
            $textFirstParagraphStart,
            $textFirstParagraphEnd - $textFirstParagraphStart + 4
        );

        return html_entity_decode(strip_tags($firstParagraph));
    }
}

<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Infrastructure\Helper\DOM\DOM;
use DOMDocument;
use LogicException;
use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class MartinFowlerFeedProvider implements FeedProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
    ) {
    }


    public static function getSource(): string
    {
        return 'martinfowler.com';
    }

    public function fetchFeedItems(): array
    {
        $response = $this->client->request('GET', 'https://martinfowler.com/feed.atom');

        $feed = new DOMDocument();
        $feed->loadXML($response->getContent());

        $newsItems = [];

        foreach ($feed->getElementsByTagName('entry') as $entry) {
            try {
                $title = DOM::getString($entry, 'title');
                $summary = DOM::getString($entry, 'content');
                $updated = DOM::getDateTime($entry, 'updated');
                $link = DOM::getLink($entry, 'link');
            } catch (OutOfBoundsException | LogicException $exception) {
                $this->logger->warning('[{source}] Failed to parse entry: {reason}.', [
                    'source' => self::getSource(),
                    'reason' => $exception->getMessage(),
                ]);

                continue;
            }

            $feedItems = new FeedItem(
                $title,
                $this->deriveFirstParagraphFromHtmlText($summary),
                $link,
                $updated,
                self::getSource()
            );

            $newsItems[] = $feedItems;
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

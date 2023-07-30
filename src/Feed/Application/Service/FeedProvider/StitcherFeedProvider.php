<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Infrastructure\Helper\DOM\DOM;
use DateTime;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use http\Client\Request;
use LogicException;
use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final readonly class StitcherFeedProvider implements FeedProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
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
        $response = $this->client->request('GET', 'https://stitcher.io/rss');

        $feed = new DOMDocument();
        $feed->loadXML($response->getContent());

        $feedItems = [];

        foreach ($feed->getElementsByTagName('entry') as $entry) {
            try {
                $rawTitle = DOM::getString($entry, 'title');
                $rawSummary = DOM::getString($entry, 'summary');
                $updated = DOM::getDateTime($entry, 'updated');
                $link = DOM::getLink($entry, 'link');
            } catch (OutOfBoundsException | LogicException $exception) {
                $this->logger->warning('[{source}] Failed to parse entry: {reason}.', [
                    'source' => self::getSource(),
                    'reason' => $exception->getMessage(),
                ]);

                continue;
            }

            $feedItems[] = new FeedItem(
                trim(html_entity_decode($rawTitle)),
                $this->deriveFirstParagraphFromHtmlText($rawSummary),
                $link,
                $updated,
                $this->getSource(),
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

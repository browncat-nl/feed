<?php

namespace App\Feed\Application\Service\FeedProvider;

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
                $rawTitle = $this->getItem($entry, 'title');
                $rawSummary = $this->getItem($entry, 'summary');
                $updated = $this->getDateTime($entry, 'updated');
                $link = self::getLink($entry, 'link');
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

    private static function getItem(DOMElement $element, string $key): string
    {
        return $element->getElementsByTagName($key)->item(0)?->firstChild?->nodeValue
            ?? throw new OutOfBoundsException(sprintf('`%s` does not exist in DOMElement', $key));
    }

    private static function getDateTime(DOMElement $element, string $key): DateTime
    {
        $value = self::getItem($element, $key);

        if (!$epoch = strtotime($value)) {
            throw new LogicException(sprintf('value `%s` could not be converted to time', $value));
        }

        $dateTime = DateTime::createFromFormat(
            'U',
            (string) $epoch
        );

        Assert::notFalse($dateTime);

        return $dateTime;
    }

    private static function getLink(DOMElement $element, string $key): string
    {
        return $element->getElementsByTagName($key)->item(0)?->getAttribute('href')
            ?? throw new OutOfBoundsException(sprintf('%s does either not exist or has no `href` attribute in DOMElement', $key));
    }
}

<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Infrastructure\Helper\DOM\DOM;
use DOMDocument;
use LogicException;
use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SymfonyFeedProvider implements FeedProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
    ) {
    }

    public static function getSource(): string
    {
        return 'symfony.com';
    }

    public function fetchFeedItems(): array
    {
        $response = $this->client->request('GET', 'https://feeds.feedburner.com/symfony/blog');

        $feed = new DOMDocument();

        $feed->loadXML($response->getContent());

        $newsItems = [];

        foreach ($feed->getElementsByTagName('item') as $entry) {
            try {
                $title = DOM::getString($entry, 'title');
                $summary = trim(str_replace(["\r", "\n"], ' ', DOM::getString($entry, 'description')));
                $updated = DOM::getDateTime($entry, 'pubDate');
                $link = DOM::getString($entry, 'link');
            } catch (OutOfBoundsException | LogicException $exception) {
                $this->logger->warning('[{source}] Failed to parse entry: {reason}.', [
                    'source' => self::getSource(),
                    'reason' => $exception->getMessage(),
                ]);

                continue;
            }

            $feedItems = new FeedItem(
                $title,
                $summary,
                $link,
                $updated,
                self::getSource()
            );

            $newsItems[] = $feedItems;
        }

        return $newsItems;
    }
}

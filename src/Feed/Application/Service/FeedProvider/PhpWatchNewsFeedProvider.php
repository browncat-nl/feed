<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Infrastructure\Helper\DOM\DOM;
use DOMDocument;
use LogicException;
use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PhpWatchNewsFeedProvider implements FeedProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
    ) {
    }


    public static function getSource(): string
    {
        return 'php.watch';
    }

    public function fetchFeedItems(): array
    {
        $response = $this->client->request('GET', 'https://php.watch/feed/news.xml');

        $feed = new DOMDocument();
        $feed->loadXML($response->getContent());

        $feedItems = [];

        foreach ($feed->getElementsByTagName('entry') as $entry) {
            try {
                $title = DOM::getString($entry, 'title');
                $summary = str_replace(["\n", "\r", "\t"], ' ', DOM::getString($entry, 'summary'));
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
                $title,
                $summary,
                $link,
                $updated,
                self::getSource()
            );
        }

        return $feedItems;
    }
}

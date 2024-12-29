<?php

namespace App\Feed\Infrastructure\FeedParser\RssParser;

use App\Feed\Application\FeedParser\FeedItem;
use App\Feed\Application\FeedParser\FeedParser;
use Psr\Log\LoggerInterface;
use SimplePie\SimplePie;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SimplePieFeedParser implements FeedParser
{
    public function __construct(private HttpClientInterface $client, private LoggerInterface $logger)
    {
    }

    public function fetchFeed(string $source, string $url): array
    {
        $psr18Client = new Psr18Client($this->client);

        $simplepie = new SimplePie();
        $simplepie->set_http_client($psr18Client, $psr18Client, $psr18Client);
        $simplepie->enable_exceptions();
        $simplepie->enable_cache(false);
        $simplepie->enable_order_by_date(false);

        $simplepie->set_feed_url($url);
        $simplepie->init();

        $feedItems = [];

        foreach ($simplepie->get_items() ?? [] as $item) {
            if (
                ($title = $item->get_title()) === null ||
                ($link = $item->get_link()) === null ||
                ($date = \DateTime::createFromFormat('U', strval($item->get_date('U')))) === false
            ) {
                $this->logger->warning('[SimplePieParser] Could not parse entry', [
                    'source' => $source,
                    'feed_url' => $url,
                    'id' => $item->get_id()
                ]);

                continue;
            }

            $feedItems[] = new FeedItem(
                html_entity_decode($title),
                trim(str_replace(["\r", "\n", "\t"], ' ', html_entity_decode($item->get_description() ?? ''))),
                html_entity_decode($link),
                $date,
                $source,
            );
        }

        return $feedItems;
    }
}

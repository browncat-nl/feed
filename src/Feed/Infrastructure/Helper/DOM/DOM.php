<?php

namespace App\Feed\Infrastructure\Helper\DOM;

use DateTime;
use DOMElement;
use LogicException;
use OutOfBoundsException;
use Webmozart\Assert\Assert;

final readonly class DOM
{
    public static function getString(DOMElement $element, string $key): string
    {
        return $element->getElementsByTagName($key)->item(0)?->firstChild->nodeValue
            ?? throw new OutOfBoundsException(sprintf('`%s` does not exist in DOMElement', $key));
    }

    public static function getDateTime(DOMElement $element, string $key): DateTime
    {
        $value = self::getString($element, $key);

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

    public static function getLink(DOMElement $element, string $key): string
    {
        $link = $element->getElementsByTagName($key)->item(0)?->getAttribute('href');

        if ($link === null || $link === '') {
            throw new OutOfBoundsException(sprintf('%s does either not exist or has no `href` attribute in DOMElement', $key));
        }

        return $link;
    }
}

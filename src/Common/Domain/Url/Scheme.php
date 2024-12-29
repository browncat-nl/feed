<?php

namespace App\Common\Domain\Url;

/**
 * List of supported schemes.
 *
 * Full list of uri schemes can be found [here](https://en.wikipedia.org/wiki/List_of_URI_schemes)
 */
enum Scheme : string
{
    case HTTPS = 'https';
    case HTTP = 'http';
}

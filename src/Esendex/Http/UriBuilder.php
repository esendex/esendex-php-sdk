<?php
namespace Esendex\Http;

class UriBuilder
{
    const HOST = "api.esendex.com";

    public static function serviceUri($version, $resource, array $parts = null, $secure = true)
    {
        $host = self::HOST;
        $scheme = ($secure) ? "https" : "http";

        $result = "{$scheme}://{$host}/{$version}/{$resource}";
        if (isset($parts)) {
            foreach ($parts as $part) {
                $encodedPart = rawurlencode($part);
                $result .= "/{$encodedPart}";
            }
        }
        return $result;
    }
}

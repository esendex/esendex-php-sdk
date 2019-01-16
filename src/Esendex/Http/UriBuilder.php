<?php
/**
 * Copyright (c) 2019, Commify Ltd.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Commify nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Http
 * @package    Esendex
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Http;

class UriBuilder
{
    const HOST = "api.esendex.com";
    private static $url_separator;

    public static function init()
    {
        self::$url_separator = ini_get('arg_separator.output');
    }

    public static function serviceUri($version, $resource, array $parts = null, $secure = true)
    {
        $host = defined("ESENDEX_API_HOST") ? ESENDEX_API_HOST : self::HOST;
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

    public static function buildQuery(array $params)
    {
        foreach ($params as $key => $value) {
            if ($value instanceof \DateTime) {
                $params[$key] = $value->format(\DateTime::ISO8601);
            }
        }
        
        if (defined("PHP_QUERY_RFC3986")) { // >= 5.4
            return http_build_query($params, '', self::$url_separator, PHP_QUERY_RFC3986);
        }

        $result = '';
        $glue = '';
        foreach ($params as $key => $value) {
            $encodedKey = rawurlencode($key);
            $encodedValue = rawurlencode($value);
            $result .= "{$glue}{$encodedKey}={$encodedValue}";
            $glue = self::$url_separator;
        }
        return $result;
    }
}
UriBuilder::init();

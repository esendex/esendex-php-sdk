<?php
/**
 * Copyright (c) 2013, Esendex Ltd.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Esendex nor the
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
 * @category   Service
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2013 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex;

class InboxService
{
    const INBOX_SERVICE = 'inbox';
    const INBOX_SERVICE_VERSION = 'v1.0';

    private $authentication;
    private $httpClient;
    private $parser;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     * @param Parser\InboxXmlParser $parser
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\InboxXmlParser $parser = null
    ) {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
        $this->parser = (isset($parser))
            ? $parser
            : new Parser\InboxXmlParser(new Parser\MessageHeaderXmlParser());
    }

    /**
     * @param int $startIndex
     * @param int $count
     * @return Model\InboxPage
     */
    public function latest($startIndex = null, $count = null)
    {
        $uri = Http\UriBuilder::serviceUri(
            self::INBOX_SERVICE_VERSION,
            self::INBOX_SERVICE,
            array($this->authentication->accountReference(), "messages"),
            $this->httpClient->isSecure()
        );

        $query = array();
        if ($startIndex != null && is_int($startIndex)) {
            $query["startIndex"] = $startIndex;
        }
        if ($count != null && is_int($count)) {
            $query["count"] = $count;
        }
        if (count($query) > 0) {
            $uri .= "?" . Http\UriBuilder::buildQuery($query);
        }
        
        $data = $this->httpClient->get(
            $uri,
            $this->authentication
        );

        return $this->parser->parse($data);
    }

    /**
     * Delete an inbox message using it's messageId
     *
     * @param string $messageId
     * @return bool
     */
    function deleteInboxMessage($messageId)
    {
        $uri = Http\UriBuilder::serviceUri(
            self::INBOX_SERVICE_VERSION,
            self::INBOX_SERVICE,
            array("messages", $messageId),
            $this->httpClient->isSecure()
        );

        return $this->httpClient->delete($uri, $this->authentication) == 200;
    }
}
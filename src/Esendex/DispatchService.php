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

class DispatchService
{
    const DISPATCH_SERVICE = "messagedispatcher";
    const DISPATCH_SERVICE_VERSION = "v1.0";
    const ACCOUNTS_SERVICE = "accounts";
    const ACCOUNTS_SERVICE_VERSION = "v1.0";

    private $authentication;
    private $httpClient;
    private $parser;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     * @param Parser\DispatchXmlParser $parser
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\DispatchXmlParser $parser = null
    ) {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
        $this->parser = (isset($parser))
            ? $parser
            : new Parser\DispatchXmlParser($authentication->accountReference());
    }

    /**
     * @param Model\DispatchMessage $message
     * @return Model\ResultItem
     * @throws Exceptions\EsendexException
     */
    public function send(Model\DispatchMessage $message)
    {
        $xml = $this->parser->encode($message);
        $uri = Http\UriBuilder::serviceUri(
            self::DISPATCH_SERVICE_VERSION,
            self::DISPATCH_SERVICE,
            null,
            $this->httpClient->isSecure()
        );

        $result = $this->httpClient->post(
            $uri,
            $this->authentication,
            $xml
        );

        $arr = $this->parser->parse($result);

        if (count($arr) >= 1) {
            return $arr[0];
        } else {
            throw new Exceptions\EsendexException("Error parsing the dispatch result", null, array('data_returned' => $result));
        }
    }

    /**
     * Get the number of remaining credits for your account
     *
     * @return int
     */
    public function getCredits()
    {
        try {
            $uri = Http\UriBuilder::serviceUri(
                self::ACCOUNTS_SERVICE_VERSION,
                self::ACCOUNTS_SERVICE,
                null,
                $this->httpClient->isSecure()
            );

            $xml = $this->httpClient->get($uri, $this->authentication);
            $accounts = new \SimpleXMLElement($xml);
            foreach ($accounts->account as $account) {
                if (strcasecmp($account->reference, $this->authentication->accountReference()) == 0) {
                    return intval($account->messagesremaining, 10);
                }
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}

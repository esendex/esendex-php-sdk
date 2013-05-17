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

class CheckAccessService
{
    const SERVICE = "accounts";
    const SERVICE_VERSION = 'v1.0';

    private $httpClient;

    public function __construct(Http\IHttp $httpClient = null)
    {
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
    }

    /**
     * Check that your username, password and account reference is valid
     *
     * @param string $accountReference
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function checkAccess($accountReference, $username, $password)
    {
        return $this->checkAuthenticationAccess(
            new Authentication\LoginAuthentication($accountReference, $username, $password)
        );
    }

    /**
     * Check if a session is valid, a valid session ID might have timed out and have
     * been deleted.  By calling this method the session will be
     * 'kept alive'
     *
     * @param Authentication\SessionAuthentication $authentication
     * @return bool
     */
    public function checkSessionAccess(Authentication\SessionAuthentication $authentication)
    {
        return $this->checkAuthenticationAccess($authentication);
    }

    /**
     * Check that any authentication is valid
     *
     * @param Authentication\IAuthentication $authentication
     * @return bool
     */
    public function checkAuthenticationAccess(Authentication\IAuthentication $authentication)
    {
        try {
            $uri = Http\UriBuilder::serviceUri(
                self::SERVICE_VERSION,
                self::SERVICE,
                null,
                $this->httpClient->isSecure()
            );

            $xml = $this->httpClient->get($uri, $authentication);
            $accounts = new \SimpleXMLElement($xml);
            foreach ($accounts->account as $account) {
                if (strcasecmp($account->reference, $authentication->accountReference()) == 0) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
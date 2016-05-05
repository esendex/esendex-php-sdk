<?php
/**
 * Copyright (c) 2016, Esendex Ltd.
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
 * @copyright  2016 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex;

class OptOutsService
{
    private $authentication;
    private $httpClient;
    private $parser;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\OptOutXmlParser $parser = null
    ) {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
        
        $this->parser = (isset($parser))
            ? $parser
            : new Parser\OptOutXmlParser();
    }

    /**
     * @param string $optOutId
     * @return Model\OptOut
     */
    public function getById($optOutId)
    {
        $uri = "https://api.esendex.com/v1.0/optouts/{$optOutId}";

        $xmlResult = $this->httpClient->get(
                         $uri,
                         $this->authentication
                     );
        
        return $this->parser->parse($xmlResult);
    }

    /**
     * @param string $optOutId
     * @return Model\OptOut
     */
    public function add($accountReference, $mobileNumber)
    {
        $uri = "https://api.esendex.com/v1.0/optouts";
        
        $xmlRequest = $this->parser->encodePostRequest($accountReference, $mobileNumber);
        
        $xmlResult = $this->httpClient->post(
                         $uri,
                         $this->authentication,
                         $xmlRequest
                     );
        
        return $this->parser->parsePostResponse($xmlResult);
    }
    
    public function get($pageNumber = null, $pageSize = null)
    {
        if($pageNumber == null)
        {
            $pageNumber = 1;
        }
        if($pageSize == null)
        {
            $pageSize = 15;
        } 
          
        $startIndex = ($pageNumber-1)*$pageSize;
        $uri = "https://api.esendex.com/v1.0/optouts?startIndex={$startIndex}";
        
        $xmlResult = $this->httpClient->get(
                         $uri,
                         $this->authentication
                     );
        
        return $this->parser->parseMultipleResult($xmlResult);
    }
}

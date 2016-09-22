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
    
    const SERVICE = "optouts";
    const SERVICE_VERSION = "v1.0";

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
        $uri = Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            array($optOutId),
            $this->httpClient->isSecure()
        );
        
        $xmlResult = $this->httpClient->get(
                         $uri,
                         $this->authentication
                     );
        
        return $this->parser->parse($xmlResult);
    }

    /**
     * @param string $accountReference
     * @param string $mobileNumber
     * @return Model\OptOut
     */
    public function add($accountReference, $mobileNumber)
    {
        $uri = Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            null,
            $this->httpClient->isSecure()
        );
        
        $xmlRequest = $this->parser->encodePostRequest($accountReference, $mobileNumber);
        
        $xmlResult = $this->httpClient->post(
                         $uri,
                         $this->authentication,
                         $xmlRequest
                     );
        
        return $this->parser->parsePostResponse($xmlResult);
    }
    
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @return array
     */
    public function get($pageNumber = null, $pageSize = null)
    {
        if($pageSize == null && $pageNumber == null)
        {
            return $this->getWithQuery();
        }
        
        $query = array();
        $query["startIndex"] = $this->calculateStartIndex($pageNumber, $pageSize);
        
        if($pageSize != null)
        {
            $query["count"] = $pageSize;
        }
        
        return $this->getWithQuery($query);
    }
    
    /**
     * @param string $from
     * @param int $pageNumber
     * @param int $pageSize
     * @return array
     */
    public function getWithFromAddress($from, $pageNumber = null, $pageSize = null)
    {
        $query = array();
        $query["startIndex"] = $this->calculateStartIndex($pageNumber, $pageSize);
        
        if($pageSize != null)
        {
            $query["count"] = $pageSize;
        }
        $query["from"] = $from;
        
        return $this->getWithQuery($query);
    }
    
    /**
     * @param string $accountReference
     * @param int $pageNumber
     * @param int $pageSize
     * @return array
     */
    public function getWithAccountReference($accountReference, $pageNumber = null, $pageSize = null)
    {
        $query = array();
        $query["startIndex"] = $this->calculateStartIndex($pageNumber, $pageSize);
        
        if($pageSize != null)
        {
            $query["count"] = $pageSize;
        }
        $query["accountReference"] = $accountReference;
        
        return $this->getWithQuery($query);
    }
    
    private function getWithQuery($params = null)
    {
         $uri = Http\UriBuilder::serviceUri(
           self::SERVICE_VERSION,
           self::SERVICE,
           null,
           $this->httpClient->isSecure()
       );

       if($params == null)
       {
           $params = array();
           $params["startIndex"] = 0;
       }
       
       $uri .= "?" . Http\UriBuilder::buildQuery($params);
       
       $xmlResult = $this->httpClient->get(
                        $uri,
                        $this->authentication
                    );
       
       return $this->parser->parseMultipleResult($xmlResult);
    }
    
    private function calculateStartIndex($pageNumber, $pageSize)
    {
        if($pageSize == null)
        {
            $pageSize = 15;    
        }
        if($pageNumber == null)
        {
            $pageNumber = 1;
        }
        
        return ($pageNumber-1)*$pageSize;
    }
}

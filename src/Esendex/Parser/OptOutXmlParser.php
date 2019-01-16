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
 * @category   Parser
 * @package    Esendex
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Parser;

use Esendex\Model\OptOut;
use Esendex\Model\Api;
use Esendex\Model\OptOutsPage;

class OptOutXmlParser
{
    public function parse($xml)
    {
        $optOut = simplexml_load_string($xml);
        return $this->parseOptOut($optOut);
    }

    public function parseOptOut($optOut)
    {
        $result = new OptOut();
        $result->id($optOut["id"]);
        $result->from($optOut->from);
        $result->accountReference($optOut->accountreference);
        $result->receivedAt($this->parseDateTime($optOut->receivedat));
        
        return $result;
    }
    
    public function parsePostResponse($xml)
    {
        $response = simplexml_load_string($xml);
        return $this->parseOptOut($response->optout);
    }
    
    public function encodePostRequest($accountReference, $phoneNumber)
    {
        if (strlen($phoneNumber) < 1)
            throw new ArgumentException("Recipient is invalid");
        if (strlen($accountReference) < 1)
            throw new ArgumentException("Account reference is invalid");

        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><optout />", 0, false, Api::NS);
        $doc->addAttribute("xmlns", Api::NS);
        $doc->accountreference = $accountReference;

        $child = $doc->addChild("from");
        $child->phonenumber = $phoneNumber;
            
        return $doc->asXML();
    }
    
    public function parseMultipleResult($xml)
    {
        $response = simplexml_load_string($xml);
        $optOuts = array();
        foreach($response->optout as $optOut)
        {
            $parsedOptOut = $this->parseOptOut($optOut);
            $optOuts[] = $parsedOptOut; 
        }
        
        $result = new OptOutsPage($response["startindex"], $response["totalcount"], $optOuts);
        
        return $result;        
    }

    private function parseDateTime($value)
    {
        return \DateTime::createFromFormat(\DateTime::ISO8601, $value);
    }
}

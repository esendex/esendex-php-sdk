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
use Esendex\Model\Api;
use Esendex\Model\Message;
use Esendex\Model\ResultItem;
use Esendex\Exceptions\ArgumentException;
use Esendex\Exceptions\XmlException;

class DispatchXmlParser
{
    private $reference;

    function __construct($accountReference)
    {
        $this->reference = $accountReference;
    }

    public function encode(\Esendex\Model\DispatchMessage $message)
    {
        if ($message->originator() != null) {
            if (ctype_digit($message->originator())) {
                if (strlen($message->originator()) > 20)
                    throw new ArgumentException("Numeric originator must be <= 20 digits");
            } else {
                if (strlen($message->originator()) > 11)
                    throw new ArgumentException("Alphanumeric originator must <= 11 characters");
                if (!preg_match("/^[a-zA-Z0-9\*\$\?\!\"\#\%\&_\-\,\.\s@'\+]{1,11}$/",
                                $message->originator()))
                    throw new ArgumentException("Alphanumeric originator contains invalid character(s)");
            }
        }
        if (strlen($message->recipient()) < 1)
            throw new ArgumentException("Recipient is invalid");
        if ($message->validityPeriod() > 72)
            throw new ArgumentException("Validity too long, must be less or equal to than 72");

        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><messages />", 0, false, Api::NS);
        $doc->addAttribute("xmlns", Api::NS);
        $doc->accountreference = $this->reference;
        if ($message->characterSet() != null)
            $doc->characterset = $message->characterSet();

        $child = $doc->addChild("message");
        if ($message->originator() != null)
            $child->from = $message->originator();
        $child->to = $message->recipient();
        $child->body = $message->body();
		    $child->type = $message->type();
        if ($message->validityPeriod() > 0)
            $child->validity = $message->validityPeriod();
        if ($message->language() != null)
            $child->lang = $message->language();
        if ($message->retries() != null)
            $child->retries = $message->retries();

        return $doc->asXML();
    }

    public function parse($xml)
    {
        $headers = simplexml_load_string($xml);
        if ($headers->getName() != "messageheaders")
            throw new XmlException("Xml is missing <messageheaders /> root element");

        $results = array();
        foreach ($headers->messageheader as $header)
        {
            $results[] = new ResultItem($header["id"], $header["uri"]);
        }

        return $results;
    }
}

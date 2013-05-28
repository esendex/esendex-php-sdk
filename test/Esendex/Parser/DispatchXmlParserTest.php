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
 * @category   Testing
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2013 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Parser;
use Esendex\Model\Api;
use Esendex\Model\Message;
use Esendex\Model\DispatchMessage;

class DispatchXmlParserTest extends \PHPUnit_Framework_TestCase
{
    const DISPATCHER_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messageheaders xmlns="https://api.esendex.com/ns/">
    <messageheader uri="https://api.esendex.com/v1.0/MessageHeaders/1183C73D-2E62-4F60-B610-30F160BDFBD5"
                   id="1183C73D-2E62-4F60-B610-30F160BDFBD5" />
</messageheaders>
XML;

    const DISPATCHER_RESPONSE_NOHEADERS_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messageheaders xmlns="https://api.esendex.com/ns/" />
XML;

    /**
     * @test
     */
    function encodeMessage()
    {
        $reference = "EX123456";
        $message = new DispatchMessage(
            "4412345678",
            "4487654321",
            "Something to say",
            Message::SmsType,
            24,
            DispatchMessage::ENGLISH_LANGUAGE
        );
        $parser = new DispatchXmlParser($reference);
        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><messages />", 0, false, Api::NS);
        $doc->addChild("accountreference", $reference);
        $child = $doc->addChild("message");
        $child->addChild("from", $message->originator());
        $child->addChild("to", $message->recipient());
        $child->addChild("body", $message->body());
        $child->addChild("type", Message::SmsType);
        $child->addChild("validity", $message->validityPeriod());
        $child->addChild("lang", $message->language());
        $expected = $doc->asXML();

        $result = $parser->encode($message);

        $this->assertEquals($expected, $result);
    }    
    
    /**
     * @test
     */
    function encodeVoiceMessage()
    {
        $reference = "EX123456";
        $message = new DispatchMessage(
            "4412345678",
            "4487654321",
            "Something to say",
            Message::VoiceType,
            36,
            "fr-FR"
        );
        $parser = new DispatchXmlParser($reference);
        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><messages />", 0, false, Api::NS);
        $doc->addChild("accountreference", $reference);
        $child = $doc->addChild("message");
        $child->addChild("from", $message->originator());
        $child->addChild("to", $message->recipient());
        $child->addChild("body", $message->body());
        $child->addChild("type", Message::VoiceType);
        $child->addChild("validity", $message->validityPeriod());
        $child->addChild("lang", $message->language());
        $expected = $doc->asXML();

        $result = $parser->encode($message);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function encodeMessageInvalidOriginator()
    {
        $reference = "EX123456";
        $message = new DispatchMessage(
            null,
            "4487654321",
            "Something to say",
            Message::SmsType
        );
        $parser = new DispatchXmlParser($reference);

        $this->setExpectedException("\\Esendex\\Exceptions\\ArgumentException", "Originator is invalid");
        $parser->encode($message);
    }

    /**
     * @test
     */
    function encodeMessageInvalidRecipient()
    {
        $reference = "EX123456";
        $message = new DispatchMessage(
            "4412345678",
            null,
            "Something to say",
            Message::SmsType
        );
        $parser = new DispatchXmlParser($reference);

        $this->setExpectedException("\\Esendex\\Exceptions\\ArgumentException", "Recipient is invalid");
        $parser->encode($message);
    }

    /**
     * @test
     */
    function encodeMessageInvalidValidity()
    {
        $reference = "EX123456";
        $message = new DispatchMessage(
            "4412345678",
            "4487654321",
            "Something to say",
            Message::SmsType,
            73
        );
        $parser = new DispatchXmlParser($reference);

        $this->setExpectedException(
            "\\Esendex\\Exceptions\\ArgumentException",
            "Validity too long, must be less or equal to than 72"
        );
        $parser->encode($message);
    }

    /**
     * @test
     */
    function encodeMessageBodyContainsAmpersand()
    {
        $reference = "EX123456";
        $message = new DispatchMessage(
            "4412345678",
            "4487654321",
            "This & That",
            Message::SmsType
        );
        $parser = new DispatchXmlParser($reference);

        $result = $parser->encode($message);
        
        $this->assertThat($result, $this->stringContains("This &amp; That"));
    }

    /**
     * @test
     */
    function parseMessageResults()
    {
        $parser = new DispatchXmlParser("reference");

        $result = $parser->parse(self::DISPATCHER_RESPONSE_XML);

        $this->assertEquals(1, count($result));

        $resultItem = $result[0];
        $this->assertInstanceOf("\\Esendex\\Model\\ResultItem", $resultItem);
        $this->assertEquals("1183C73D-2E62-4F60-B610-30F160BDFBD5", $resultItem->id());
        $this->assertEquals(
            "https://api.esendex.com/v1.0/MessageHeaders/1183C73D-2E62-4F60-B610-30F160BDFBD5",
            $resultItem->uri()
        );
    }

    /**
     * @test
     */
    function parseMessageResultsWithoutHeaders()
    {
        $parser = new DispatchXmlParser("reference");

        $result = $parser->parse(self::DISPATCHER_RESPONSE_NOHEADERS_XML);

        $this->assertEquals(0, count($result));
    }

    /**
     * @test
     */
    function parseMessageResultsUnexpectedXml()
    {
        $parser = new DispatchXmlParser("reference");

        $this->setExpectedException("\\Esendex\\Exceptions\\XmlException");
        $parser->parse("<?xml version=\"1.0\" encoding=\"utf-8\"?><wrong />");
    }
}

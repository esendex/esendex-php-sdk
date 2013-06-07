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
use Esendex\Model\Message;

class MessageHeaderXmlParserTest extends \PHPUnit_Framework_TestCase
{
    const OUTBOUND_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messageheader id="a22702be-881e-43d9-9790-7646a95335f6"
               uri="https://api.esendex.com/v1.0/messageheaders/a22702be-881e-43d9-9790-7646a95335f6"
               xmlns="http://api.esendex.com/ns/">
    <reference>EX123456</reference>
    <status>Delivered</status>
    <deliveredat>2013-03-06T13:20:00Z</deliveredat>
    <sentat>2013-03-06T13:19:20.177Z</sentat>
    <laststatusat>2013-03-06T13:20:00Z</laststatusat>
    <submittedat>2013-03-06T13:18:25.437Z</submittedat>
    <type>SMS</type>
    <to>
        <phonenumber>447123456789</phonenumber>
    </to>
    <from>
        <phonenumber>447987654321</phonenumber>
    </from>
    <summary>Every message matters</summary>
    <body id="a22702be-881e-43d9-9790-7646a95335f6"
          uri="https://api.esendex.com/v1.0/messageheaders/a22702be-881e-43d9-9790-7646a95335f6/body" />
    <direction>Outbound</direction>
    <parts>1</parts>
    <username>support@esendex.com</username>
</messageheader>
XML;

    /**
     * @test
     */
    function parseOutboundMessage()
    {
        $parser = new MessageHeaderXmlParser();

        $result = $parser->parse(self::OUTBOUND_RESPONSE_XML);

        $this->assertInstanceOf("\\Esendex\\Model\\SentMessage", $result);

        $this->assertEquals("a22702be-881e-43d9-9790-7646a95335f6", $result->id());
        $this->assertEquals("447987654321", $result->originator());
        $this->assertEquals("447123456789", $result->recipient());
        $this->assertEquals("Delivered", $result->status());
        $this->assertEquals(Message::SmsType, $result->type());
        $this->assertEquals(Message::Outbound, $result->direction());
        $this->assertEquals(1, $result->parts());
        $this->assertEquals(
            "https://api.esendex.com/v1.0/messageheaders/a22702be-881e-43d9-9790-7646a95335f6/body",
            $result->bodyUri()
        );
        $this->assertEquals("Every message matters", $result->summary());
        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, "2013-03-06T13:20:00Z"),
            $result->lastStatusAt()
        );

        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, "2013-03-06T13:18:25Z"),
            $result->submittedAt()
        );
        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, "2013-03-06T13:19:20Z"),
            $result->sentAt()
        );
        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, "2013-03-06T13:20:00Z"),
            $result->deliveredAt()
        );
        $this->assertEquals("support@esendex.com", $result->username());
    }

    const INBOX_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messageheader id="d6258601-3442-484f-b2ac-300088a8a4d4"
               uri="https://api.esendex.com/v1.0/messageheaders/d6258601-3442-484f-b2ac-300088a8a4d4"
               xmlns="http://api.esendex.com/ns/">
    <reference>EX123456</reference>
    <status>Submitted</status>
    <sentat>2013-03-06T14:30:42.407Z</sentat>
    <laststatusat>2013-03-06T14:30:42.407Z</laststatusat>
    <submittedat>2013-03-06T14:30:42.407Z</submittedat>
    <receivedat>2013-03-06T14:30:42.407Z</receivedat>
    <type>SMS</type>
    <to>
        <phonenumber>447123456789</phonenumber>
    </to>
    <from>
        <phonenumber>447987654321</phonenumber>
    </from>
    <summary>Every message matters</summary>
    <body id="d6258601-3442-484f-b2ac-300088a8a4d4"
          uri="https://api.esendex.com/v1.0/messageheaders/d6258601-3442-484f-b2ac-300088a8a4d4/body" />
    <direction>Inbound</direction>
    <parts>1</parts>
    <username />
    <readat>0001-01-01T00:00:00Z</readat>
</messageheader>
XML;

    /**
     * @test
     */
    function parseInboundMessage()
    {
        $parser = new MessageHeaderXmlParser();

        $result = $parser->parse(self::INBOX_RESPONSE_XML);

        $this->assertInstanceOf("\\Esendex\\Model\\InboxMessage", $result);

        $this->assertEquals("d6258601-3442-484f-b2ac-300088a8a4d4", $result->id());
        $this->assertEquals("447987654321", $result->originator());
        $this->assertEquals("447123456789", $result->recipient());
        $this->assertEquals("Submitted", $result->status());
        $this->assertEquals(Message::SmsType, $result->type());
        $this->assertEquals(Message::Inbound, $result->direction());
        $this->assertEquals(1, $result->parts());
        $this->assertEquals(
            "https://api.esendex.com/v1.0/messageheaders/d6258601-3442-484f-b2ac-300088a8a4d4/body",
            $result->bodyUri()
        );
        $this->assertEquals("Every message matters", $result->summary());
        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, "2013-03-06T14:30:42Z"),
            $result->lastStatusAt()
        );

        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, "2013-03-06T14:30:42Z"),
            $result->receivedAt()
        );
        $this->assertNull($result->readAt());
        $this->assertNull($result->readBy());
    }

    /**
     * @test
     */
    function parseReadInboundMessage()
    {
        $readAt = "2013-03-07T15:20:12Z";
        $readBy = "support@esendex.com";
        $message = simplexml_load_string(self::INBOX_RESPONSE_XML);
        $message->readat = $readAt;
        $message->addChild("readby", $readBy);

        $parser = new MessageHeaderXmlParser();

        $result = $parser->parse($message->asXML());

        $this->assertEquals(
            \DateTime::createFromFormat(DATE_ISO8601, $readAt),
            $result->readAt()
        );
        $this->assertEquals($readBy, $result->readBy());
    }
}

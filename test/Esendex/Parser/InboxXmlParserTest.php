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

class InboxXmlParserTest extends \PHPUnit_Framework_TestCase
{
    private $headerParser;
    private $parser;

    function setUp()
    {
        $this->headerParser = $this->getMockBuilder("\\Esendex\\Parser\\MessageHeaderXmlParser")
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new InboxXmlParser($this->headerParser);
    }

    const INBOX_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messageheaders startindex="4" count="3" totalcount="9"
                xmlns="http://api.esendex.com/ns/">
  <messageheader id="d6258601-3442-484f-b2ac-300088a8a4d4"
                 uri="https://api.esendex.com/v1.0/messageheaders/d6258601-3442-484f-b2ac-300088a8a4d4">
    <reference>EX123456</reference>
    <status>Submitted</status>
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
    <readat>0001-01-01T00:00:00Z</readat>
  </messageheader>
  <messageheader id="3e73eb78-65cd-42b4-a0b7-5b8a29603ed1"
                 uri="https://api.esendex.com/v1.0/messageheaders/3e73eb78-65cd-42b4-a0b7-5b8a29603ed1">
    <reference>EX123456</reference>
    <status>Submitted</status>
    <receivedat>2013-03-06T14:26:37.91Z</receivedat>
    <type>SMS</type>
    <to>
      <phonenumber>447123456789</phonenumber>
    </to>
    <from>
      <phonenumber>447987654321</phonenumber>
    </from>
    <summary>Every message matters</summary>
    <body id="3e73eb78-65cd-42b4-a0b7-5b8a29603ed1"
          uri="https://api.esendex.com/v1.0/messageheaders/3e73eb78-65cd-42b4-a0b7-5b8a29603ed1/body" />
    <direction>Inbound</direction>
    <parts>1</parts>
    <readat>2013-03-06T22:33:45.097Z</readat>
    <readby>support@esendex.com</readby>
  </messageheader>
  <messageheader id="c1bc3609-1f79-4346-9cb7-d5b15cd8eb11"
                 uri="https://api.esendex.com/v1.0/messageheaders/c1bc3609-1f79-4346-9cb7-d5b15cd8eb11">
    <reference>EX123456</reference>
    <status>Submitted</status>
    <receivedat>2012-10-26T11:02:38.963Z</receivedat>
    <type>SMS</type>
    <to>
      <phonenumber>447123456789</phonenumber>
    </to>
    <from>
      <phonenumber>447123456789</phonenumber>
    </from>
    <summary>Every message matters</summary>
    <body id="c1bc3609-1f79-4346-9cb7-d5b15cd8eb11"
          uri="https://api.esendex.com/v1.0/messageheaders/c1bc3609-1f79-4346-9cb7-d5b15cd8eb11/body" />
    <direction>Inbound</direction>
    <parts>1</parts>
    <readat>2012-11-16T14:52:24.133Z</readat>
    <readby>support@esendex.com</readby>
  </messageheader>
</messageheaders>
XML;

    /**
     * @test
     */
    function parseXmlWithResults()
    {
        $this->headerParser
            ->expects($this->exactly(3))
            ->method("parseHeader")
            ->will($this->returnValue(new \Esendex\Model\InboxMessage()));

        $result = $this->parser->parse(self::INBOX_RESPONSE_XML);

        $this->assertInstanceOf("\\Esendex\\Model\\InboxPage", $result);
        $this->assertEquals(4, $result->startIndex());
        $this->assertEquals(9, $result->totalCount());
        $this->assertEquals(3, count($result));
        foreach ($result as $key => $value) {
            $this->assertTrue(is_int($key));
            $this->assertInstanceOf("\\Esendex\\Model\\InboxMessage", $value);
        }
    }

    const INBOX_NORESULTS_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messageheaders startindex="100" count="0" totalcount="100"
                xmlns="http://api.esendex.com/ns/" />
XML;

    /**
     * @test
     */
    function parseXmlWithNoResults()
    {
        $this->headerParser
            ->expects($this->never())
            ->method("parseHeader");

        $result = $this->parser->parse(self::INBOX_NORESULTS_RESPONSE_XML);

        $this->assertInstanceOf("\\Esendex\\Model\\InboxPage", $result);
        $this->assertEquals(100, $result->startIndex());
        $this->assertEquals(100, $result->totalCount());
        $this->assertEquals(0, count($result));
    }
}

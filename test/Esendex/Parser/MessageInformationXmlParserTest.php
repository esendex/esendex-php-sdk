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
use Esendex\Model\MessageBody;
use Esendex\Model\MessageInformation;

class MessageInformationXmlParserTest extends \PHPUnit_Framework_TestCase
{
    function characterSets()
    {
        return array(
            array(MessageBody::CharsetGSM),
            array(MessageBody::CharsetUnicode),
            array(MessageBody::CharsetAuto)
        );
    }

    /**
     * @test
     * @dataProvider characterSets
     */
    function encodeRequest($characterSet)
    {
        $message = "the message";
        $parser = new MessageInformationXmlParser();
        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><messages />", 0, false, Api::NS);
        $doc->addAttribute("xmlns", Api::NS);
        $child = $doc->addChild("message");
        $child->addChild("body", $message);
        $child->addChild("characterset", $characterSet);
        $expected = $doc->asXML();

        $result = $parser->encode($message, $characterSet);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function encodeRequestInvalidCharacterSet()
    {
        $parser = new MessageInformationXmlParser();

        $this->setExpectedException(
            "\\Esendex\\Exceptions\\ArgumentException",
            "characterSet value was 'Latin1' and must be one of 'GSM', " .
            "'Unicode' or 'Auto'"
        );

        $result = $parser->encode("a message", "Latin1");
    }

    /**
     * @test
     */
    function encodeMessageBodyContainingXmlEntities()
    {
        $parser = new MessageInformationXmlParser();

        $result = $parser->encode("This & <That>", MessageBody::CharsetAuto);
        
        $this->assertThat(
            $result,
            $this->stringContains("This &amp; &lt;That&gt;")
        );
    }

    const RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<response xmlns="http://api.esendex.com/ns/">
 <messages>
  <message elementat="0">
   <parts>2</parts>
   <characterset>GSM</characterset>
   <availablecharactersinlastpart>53</availablecharactersinlastpart>
  </message>
 </messages>
 <errors />
</response>
XML;

    /**
     * @test
     */
    function parseResponse()
    {
        $parser = new MessageInformationXmlParser();

        $result = $parser->parse(self::RESPONSE_XML);

        $this->assertContainsOnlyInstancesOf("\\Esendex\\Model\\MessageInformation", $result);
        $this->assertEquals(1, count($result));

        $this->assertEquals(2, $result[0]->parts());
        $this->assertEquals(MessageBody::CharsetGSM, $result[0]->characterSet());
        $this->assertEquals(53, $result[0]->availableCharactersInLastPart());
    }
}

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

use Esendex\Model\OptOut;

class OptOutXmlParserTest extends \PHPUnit_Framework_TestCase
{
    const OPTOUT_RESPONSE_XML = "<optout id=\"47a1144b-8a68-4608-9360-d4a52aaf90d2\">
                                    <accountreference>EX0012345</accountreference>
                                    <from>
                                        <phonenumber>44712345678</phonenumber>
                                    </from>
                                    <receivedat>2016-10-10T13:00:00.123Z</receivedat>
                                 </optout>";
                                 
    const EXPECTED_POST_ENCODE_RESULT = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                             <optout xmlns=\"http://api.esendex.com/ns/\">
                                <accountreference>EX0012345</accountreference>
                                <from>
                                   <phonenumber>44712345678</phonenumber>
                                </from>
                             </optout>";
    
    const OPTOUTS_RESPONSE_XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                                  <optouts startindex=\"0\" count=\"2\" totalcount=\"6\" xmlns=\"http://api.esendex.com/ns/\">
                                     <optout id=\"47a1144b-8a68-4608-9360-d4a52aaf90d2\">
                                        <accountreference>EX0012345</accountreference>
                                        <receivedat>2015-11-09T15:18:19.0333333Z</receivedat>
                                        <from>
                                           <phonenumber>447728693893</phonenumber>
                                        </from>
                                     </optout>
                                     <optout id=\"47a1144b-8a68-4608-9360-d4a52aaf90d2\">
                                        <accountreference>EX0012346</accountreference>
                                        <receivedat>2015-11-10T15:00:19.0333333Z</receivedat>
                                        <from>
                                           <phonenumber>44712345678</phonenumber>
                                        </from>
                                     </optout>
                                  </optouts>";

    /**
     * @test
     */
    function encodePostRequest()
    {
        $parser = new OptOutXmlParser();
        $result = $parser->encodePostRequest("EX0012345", "44712345678");
        
        $formattedResult = preg_replace('/\s/', '', $result);
        $formattedExpectedResult = preg_replace('/\s/', '', self::EXPECTED_POST_ENCODE_RESULT);
        $this->assertSame($formattedExpectedResult, $formattedResult);
    }

    /**
     * @test
     */
    function parseOptOut()
    {
        $parser = new OptOutXmlParser();

        $result = $parser->parse(self::OPTOUT_RESPONSE_XML);
        
        $this->assertInstanceOf("\\Esendex\\Model\\OptOut", $result);

        $this->assertEquals("47a1144b-8a68-4608-9360-d4a52aaf90d2", $result->id());
        $this->assertEquals("EX0012345", $result->accountReference());
        $this->assertEquals("44712345678", $result->from()->phoneNumber());
        $this->assertEquals(
            \DateTime::createFromFormat(\DateTime::ISO8601, "2016-10-10T13:00:00.1234567Z"),
            $result->receivedAt()
        );
    }

    /**
     * @test
     */
    function parsePostResponse()
    {
        $parser = new OptOutXmlParser();
        
        $postResponse = "<response xmlns=\"http://api.esendex.com/ns/\">".self::OPTOUT_RESPONSE_XML."</response>";
        
        $result = $parser->parsePostResponse($postResponse);
        
        $this->assertInstanceOf("\\Esendex\\Model\\OptOut", $result);

        $this->assertEquals("47a1144b-8a68-4608-9360-d4a52aaf90d2", $result->id());
        $this->assertEquals("EX0012345", $result->accountReference());
        $this->assertEquals("44712345678", $result->from()->phoneNumber());
        $this->assertEquals(
            \DateTime::createFromFormat(\DateTime::ISO8601, "2016-10-10T13:00:00.1234567Z"),
            $result->receivedAt()
        );
    }
    
    /**
     * @test
     */
    function parseMultipleResult()
    {
        $parser = new OptOutXmlParser();
        
        $result = $parser->parseMultipleResult(self::OPTOUTS_RESPONSE_XML);
        
        $this->assertEquals("47a1144b-8a68-4608-9360-d4a52aaf90d2", $result[0]->id());
        $this->assertEquals("EX0012345", $result[0]->accountReference());
        $this->assertEquals("447728693893", $result[0]->from()->phoneNumber());
        $expectedDate = \DateTime::createFromFormat(\DateTime::ISO8601, "2016-10-10T13:00:00.1234567Z");
        echo "THIS IS WHAT I EXPECT: ".$expectedDate;
        $this->assertEquals(
            $expectedDate,
            $result[0]->receivedAt()
        );
    }
}

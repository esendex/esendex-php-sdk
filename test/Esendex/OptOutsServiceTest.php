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
namespace Esendex;

use Esendex\Model\OptOut;

class OptOutsServiceTest extends \PHPUnit_Framework_TestCase
{
    const OPTOUT_XML_RESPONSE = "<optout id=\"47a1144b-8a68-4608-9360-d4a52aaf90d2\">
                                    <accountreference>EX0012345</accountreference>
                                    <from>
                                        <phonenumber>44721345678</phonenumber>
                                    </from>
                                    <receivedat>10-10-2016T13:00:00.1234567Z</receivedat>
                                 </optout>";
    private $username;
    private $password;
    private $reference;
    private $authentication;
    private $httpUtil;
    private $service;
    private $optOutId;

    public $parser;

    function setUp()
    {
        $this->optOutId = "47a1144b-8a68-4608-9360-d4a52aaf90d2";
        $this->username = "jhdkfjh";
        $this->password = "dklfjlsdjkf";
        $this->reference = "ex00123456";
        $this->authentication = new Authentication\LoginAuthentication(
            $this->reference,
            $this->username,
            $this->password
        );

        $this->httpUtil = $this->getMock("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(""));
        
        $this->parser = $this->getMockBuilder("\\Esendex\\Parser\\OptOutXmlParser")
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new OptOutsService($this->authentication, $this->httpUtil, $this->parser);
    }

    /**
     * @test
     */
    function getById()
    {
        $expectedOptOut = new OptOut();
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/optouts/{$this->optOutId}"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue(true));
            
        $this->parser->expects($this->any())
            ->method("parse")
            ->will($this->returnValue($expectedOptOut));

        $result = $this->service->getById($this->optOutId);
        
        $this->assertSame($expectedOptOut, $result);
    }

    /**
     * @test
     */
    function add()
    {
        $expectedRequest = "<optout><accountreference>EX123456</accountreference><from><phonenumber>447712345678</phonenumber><from><optout>";
        $expectedOptOut = new OptOut();
        $mobileNumber = "447712345678";
        $this->parser->expects($this->any())
            ->method("encodePostRequest")
            ->will($this->returnValue($expectedRequest));
            
        $this->httpUtil
            ->expects($this->once())
            ->method("post")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/optouts"
            ),
            $this->equalTo($this->authentication),
            $this->equalTo($expectedRequest)
        )
            ->will($this->returnValue(true));
            
        $this->parser->expects($this->any())
            ->method("parsePostResponse")
            ->will($this->returnValue($expectedOptOut));

        $result = $this->service->add($this->reference, $mobileNumber);
        
        $this->assertSame($expectedOptOut, $result);
    }
    
    /**
     * @test
     */
    function get()
    {
        $expectedOptOuts = array();
        $expectedOptOuts[] = new OptOut();
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/optouts?startIndex=0"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue(true));
            
        $this->parser->expects($this->any())
            ->method("parseMultipleResult")
            ->will($this->returnValue($expectedOptOuts));

        $result = $this->service->get();
        
        $this->assertSame($expectedOptOuts, $result);
    }
}

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
 * @category   Testing
 * @package    Esendex
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex;

use Esendex\Model\MessageBody;
use Esendex\Model\MessageInformation;

class MessageInformationServiceTest extends  \PHPUnit\Framework\TestCase
{
    private $reference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $parser;
    private $service;

    function setUp() : void
    {
        $this->reference = "EX123456";
        $this->username = "jhdkfjh";
        $this->password = "dklfjlsdjkf";
        $this->authentication = new Authentication\LoginAuthentication(
            $this->reference,
            $this->username,
            $this->password
        );

        $this->httpUtil = $this->getMockForAbstractClass("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(true));

        $this->parser = $this->getMockBuilder("\\Esendex\\Parser\\MessageInformationXmlParser")
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->service = new MessageInformationService(
            $this->authentication,
            $this->httpUtil,
            $this->parser
        );
    }

    /**
     * @test
     */
    function getMessageInformationWithDefaults()
    {
        $message = "the message";
        $request = "xml request";
        $response = "xml response";
        $messageInformation = new MessageInformation();

        $this->parser
            ->expects($this->once())
            ->method("encode")
            ->with(
                $this->equalTo($message),
                $this->equalTo(MessageBody::CharsetGSM)
            )
            ->will($this->returnValue($request));
        $this->httpUtil
            ->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo(
                    "https://api.esendex.com/v1.0/messages/information"
                ),
                $this->equalTo($this->authentication),
                $this->equalTo($request)
            )
            ->will($this->returnValue($response));
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue(array($messageInformation)));

        $result = $this->service->getInformation($message);

        $this->assertSame($messageInformation, $result);
    }

    /**
     * @test
     */
    function getMessageInformationWithSpecificCharset()
    {
        $message = "the message";
        $request = "xml request";
        $response = "xml response";
        $messageInformation = new MessageInformation();

        $this->parser
            ->expects($this->once())
            ->method("encode")
            ->with(
                $this->equalTo($message),
                $this->equalTo(MessageBody::CharsetAuto)
            )
            ->will($this->returnValue($request));
        $this->httpUtil
            ->expects($this->once())
            ->method("post")
            ->with(
                $this->equalTo(
                    "https://api.esendex.com/v1.0/messages/information"
                ),
                $this->equalTo($this->authentication),
                $this->equalTo($request)
            )
            ->will($this->returnValue($response));
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue(array($messageInformation)));

        $result = $this->service->getInformation($message, MessageBody::CharsetAuto);

        $this->assertSame($messageInformation, $result);
    }

    function unexpectedResponses()
    {
        return array(
            array(array()),
            array(array(
                new MessageInformation(),
                new MessageInformation()
            ))
        );
    }

    /**
     * @test
     * @dataProvider unexpectedResponses
     */
    function getMessageInformationWithUnexpectedResponse($response)
    {
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->will($this->returnValue($response));

        $this->expectException(
            "\\Esendex\\Exceptions\\EsendexException",
            "Error parsing the result",
            null
        );

        $result = $this->service->getInformation("a message");
    }
}

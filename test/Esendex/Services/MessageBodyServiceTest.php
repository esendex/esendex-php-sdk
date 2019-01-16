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

class MessageBodyServiceTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGEBODY_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messagebody xmlns="http://api.esendex.com/ns/">
    <bodytext>Merci</bodytext>
    <characterset>GSM</characterset>
</messagebody>
XML;

    const SMART_MESSAGEBODY_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messagebody xmlns="http://api.esendex.com/ns/">
    <bodytext>0000000000000001514616475841840000484845A164632269844</bodytext>
</messagebody>
XML;

    private $messageId;
    private $accountReference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $service;

    function setUp()
    {
        $this->messageId = uniqid();
        $this->accountReference = "asjkdhlajksdhla";
        $this->username = "jhdkfjh";
        $this->password = "dklfjlsdjkf";
        $this->authentication = new Authentication\LoginAuthentication($this->accountReference, $this->username, $this->password);

        $this->httpUtil = $this->getMock("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(true));

        $this->service = new MessageBodyService($this->authentication, $this->httpUtil);
    }

    /**
     * @test
     */
    function getMessageBodyById()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/messageheaders/{$this->messageId}/body"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue(self::MESSAGEBODY_RESPONSE_XML));

        $messageBody = $this->service->getMessageBodyById($this->messageId);

        $this->assertEquals("Merci", $messageBody);
        $this->assertInstanceOf("\\Esendex\\Model\\MessageBody", $messageBody);
        $this->assertEquals("Merci", $messageBody->bodyText());
        $this->assertEquals(MessageBody::CharsetGSM, $messageBody->characterSet());
    }

    /**
     * @test
     */
    function getMessageBodyByIdWhenNullId()
    {
        $this->setExpectedException("\\Esendex\\Exceptions\\ArgumentException", "messageId is null");

        $this->service->getMessageBodyById(null);
    }

    /**
     * @test
     */
    function getMessageBodyByIdWhenNotString()
    {
        $this->setExpectedException("\\Esendex\\Exceptions\\ArgumentException", "messageId is not a string");

        $this->service->getMessageBodyById(99);
    }

    /**
     * @test
     */
    function getMessageBodyWithMessageBodyUri()
    {
        $bodyUri = "https://api.esendex.com/v1.0/messageheaders/{$this->messageId}/body";
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($bodyUri))
            ->will($this->returnValue(self::MESSAGEBODY_RESPONSE_XML));

        $messageBody = $this->service->getMessageBody($bodyUri);

        $this->assertEquals("Merci", $messageBody);
    }

    /**
     * @test
     */
    function getSmartMessageBodyWithMessageBodyUri()
    {
        $bodyUri = "https://api.esendex.com/v1.0/messageheaders/{$this->messageId}/body";
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($bodyUri))
            ->will($this->returnValue(self::SMART_MESSAGEBODY_XML));

        $messageBody = $this->service->getMessageBody($bodyUri);

        $this->assertEquals("0000000000000001514616475841840000484845A164632269844", $messageBody);
        $this->assertEquals("None", $messageBody->characterSet());
    }

    /**
     * @test
     */
    function getMessageBodyWithMessageHeader()
    {
        $bodyUri = "https://api.esendex.com/v1.0/messageheaders/{$this->messageId}/body";
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo($bodyUri))
            ->will($this->returnValue(self::MESSAGEBODY_RESPONSE_XML));

        $messageHeader = new Model\SentMessage();
        $messageHeader->bodyUri($bodyUri);
        $messageBody = $this->service->getMessageBody($messageHeader);

        $this->assertEquals("Merci", $messageBody);
    }

    /**
     * @test
     */
    function getMessageBodyWhenInvalidInput()
    {
        $this->setExpectedException(
            "\\Esendex\\Exceptions\\ArgumentException",
            "Should be either MessageBody Uri or ResultMessage"
        );

        $this->service->getMessageBody(99);
    }
}

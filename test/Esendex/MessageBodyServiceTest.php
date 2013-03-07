<?php
namespace Esendex;

class MessageBodyServiceTest extends \PHPUnit_Framework_TestCase
{
    const MESSAGEBODY_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<messagebody xmlns="http://api.esendex.com/ns/">
    <bodytext>Merci</bodytext>
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
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->will($this->returnValue(self::MESSAGEBODY_RESPONSE_XML));

        $bodyUri = "https://api.esendex.com/v1.0/messageheaders/{$this->messageId}/body";
        $messageBody = $this->service->getMessageBody($bodyUri);

        $this->assertEquals("Merci", $messageBody);
    }

    /**
     * @test
     */
    function getMessageBodyWithMessageHeader()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->will($this->returnValue(self::MESSAGEBODY_RESPONSE_XML));

        $messageHeader = new Model\SentMessage();
        $messageHeader->bodyUri("https://api.esendex.com/v1.0/messageheaders/{$this->messageId}/body");
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

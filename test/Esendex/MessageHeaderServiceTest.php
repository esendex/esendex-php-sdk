<?php
namespace Esendex;

class MessageHeaderServiceTest extends \PHPUnit_Framework_TestCase
{
    private $messageId;
    private $reference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $parser;
    private $service;

    function setUp()
    {
        $this->messageId = uniqid();
        $this->reference = "asjkdhlajksdhla";
        $this->username = "jhdkfjh";
        $this->password = "dklfjlsdjkf";
        $this->authentication = new Authentication\LoginAuthentication(
            $this->reference,
            $this->username,
            $this->password
        );

        $this->httpUtil = $this->getMock("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(true));

        $this->parser = $this->getMockBuilder("\\Esendex\\Parser\\MessageHeaderXmlParser")
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new MessageHeaderService($this->authentication, $this->httpUtil, $this->parser);
    }

    /**
     * @test
     */
    function messageWithValidMessageHeaderId()
    {
        $response = "xml response";
        $messageHeader = new Model\SentMessage();

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/messageheaders/{$this->messageId}"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue($response));
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue($messageHeader));

        $result = $this->service->message($this->messageId);

        $this->assertSame($messageHeader, $result);
    }
}

<?php
namespace Esendex;

class DispatchServiceTest extends \PHPUnit_Framework_TestCase
{
    private $reference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $service;

    public $parser;

    function setUp()
    {
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

        $this->parser = $this->getMockBuilder("\\Esendex\\Parser\\DispatchXmlParser")
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new DispatchService($this->authentication, $this->httpUtil, $this->parser);
    }

    /**
     * @test
     */
    function sendSuccess()
    {
        $message = new Model\DispatchMessage("DispatcherTest", "447712345678", "Message Body", Model\Message::SmsType);
        $request = "xml request";
        $response = "xml response";
        $resultItem = new \Esendex\Model\ResultItem(
            "1183C73D-2E62-4F60-B610-30F160BDFBD5",
            "https://api.esendex.com/v1.0/MessageHeaders/1183C73D-2E62-4F60-B610-30F160BDFBD5"
        );

        $this->parser
            ->expects($this->once())
            ->method("encode")
            ->with($this->equalTo($message))
            ->will($this->returnValue($request));
        $this->httpUtil
            ->expects($this->once())
            ->method("post")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/messagedispatcher"
            ),
            $this->equalTo($this->authentication),
            $this->equalTo($request)
        )
            ->will($this->returnValue($response));
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue(array($resultItem)));

        $result = $this->service->send($message);

        $this->assertSame($resultItem, $result);
    }

    /**
     * @test
     */
    function sendFailure()
    {
        $message = new Model\DispatchMessage("DispatcherTest", "447712345678", "Message Body", Model\Message::SmsType);
        $this->parser
            ->expects($this->any())
            ->method("parse")
            ->will($this->returnValue(array()));

        $this->setExpectedException(
            "\\Esendex\\Exceptions\\EsendexException",
            "Error parsing the dispatch result",
            null
        );

        $this->service->send($message);
    }
}

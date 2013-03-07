<?php
namespace Esendex;

class InboxServiceTest extends \PHPUnit_Framework_TestCase
{
    private $reference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $parser;
    private $service;

    function setUp()
    {
        $this->reference = "asjkdhlajksdhla";
        $this->username = "jhdkfjh";
        $this->password = "dklfjlsdjkf";
        $this->authentication = new Authentication\LoginAuthentication($this->reference, $this->username, $this->password);

        $this->httpUtil = $this->getMock("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(true));

        $this->parser = $this->getMockBuilder("\\Esendex\\Parser\\InboxXmlParser")
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new InboxService($this->authentication, $this->httpUtil, $this->parser);
    }

    /**
     * @test
     */
    function latestReturnsInboxPage()
    {
        $response = "xml response";
        $inboxPage = new Model\InboxPage(0, 10);

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/inbox/{$this->reference}/messages"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue($response));
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue($inboxPage));

        $result = $this->service->latest();

        $this->assertSame($inboxPage, $result);
    }

    /**
     * @test
     */
    function latestWithStartIndexForPageStart()
    {
        $startIndex = 2;

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/inbox/{$this->reference}/messages?startIndex={$startIndex}"
            ),
            $this->equalTo($this->authentication)
        );

        $this->service->latest($startIndex);
    }

    /**
     * @test
     */
    function latestWithStartIndexAndCountToPage()
    {
        $startIndex = 11;
        $count = 10;

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/inbox/{$this->reference}/messages" .
                    "?startIndex={$startIndex}&count={$count}"
            ),
            $this->equalTo($this->authentication)
        );

        $this->service->latest($startIndex, $count);
    }

    /**
     * @test
     */
    function latestWithCountAloneLimitsReturnedResults()
    {
        $count = 2;

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/inbox/{$this->reference}/messages?count={$count}"
            ),
            $this->equalTo($this->authentication)
        );

        $this->service->latest(null, $count);
    }

    /**
     * @test
     */
    function deleteInboxMessageSuccess()
    {
        $messageId = uniqid();

        $this->httpUtil
            ->expects($this->once())
            ->method("delete")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/inbox/messages/{$messageId}"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue(200));

        $this->assertTrue($this->service->deleteInboxMessage($messageId));
    }

    /**
     * @test
     */
    function deleteInboxMessageFailure()
    {
        $messageId = uniqid();

        $this->httpUtil
            ->expects($this->once())
            ->method("delete")
            ->will($this->returnValue(404));

        $this->assertFalse($this->service->deleteInboxMessage($messageId));
    }
}

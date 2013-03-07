<?php
namespace Esendex;

class SessionServiceTest extends \PHPUnit_Framework_TestCase
{
    private $httpUtil;
    private $service;

    const SESSION_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<session xmlns="http://api.esendex.com/ns/">
    <id>4af13f2a-6f49-42cf-8e01-ae31f28466d2</id>
</session>
XML;

    function setUp()
    {
        $this->httpUtil = $this->getMock("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(true));
        $this->service = new SessionService($this->httpUtil);
    }

    /**
     * @test
     */
    function startSessionWithUsernameAndPassword()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("post")
            ->with(
            $this->equalTo("https://api.esendex.com/v1.0/session/constructor"),
            $this->isInstanceOf("\\Esendex\\Authentication\\LoginAuthentication"),
            $this->isEmpty()
        )
            ->will($this->returnValue(self::SESSION_RESPONSE_XML));

        $result = $this->service->startSession("reference", "user", "password");

        $this->assertInstanceOf("\\Esendex\\Authentication\\SessionAuthentication", $result);
    }
}

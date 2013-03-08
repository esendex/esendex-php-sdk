<?php
namespace Esendex\Authentication;

class SessionAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function serialiseReturnsExpectedValue()
    {
        $reference = "EX000999";
        $sessionId = uniqid();

        $authentication = new SessionAuthentication($reference, $sessionId);
        $result = $authentication->getEncodedValue();

        $this->assertEquals("Basic " . base64_encode("{$sessionId}"), $result);
    }
}
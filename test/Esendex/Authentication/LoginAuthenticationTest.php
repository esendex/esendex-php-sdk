<?php
namespace Esendex\Authentication;

class LoginAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function serialiseReturnsExpectedValue()
    {
        $reference = "EX000999";
        $username = "User123";
        $password = "Password987";

        $authentication = new LoginAuthentication($reference, $username, $password);
        $result = $authentication->getEncodedValue();

        $this->assertEquals("Basic " . base64_encode("{$username}:{$password}"), $result);
    }
}

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

class CheckAccessServiceTest extends \PHPUnit_Framework_TestCase
{
    const ACCOUNTS_RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<accounts xmlns="http://api.esendex.com/ns/">
    <account id="33efe83d-f1ac-44d9-929f-2c65c937aad3" uri="https://api.esendex.com/v1.0/accounts/33efe83d-f1ac-44d9-929f-2c65c937aad3">
        <reference>EX123456</reference>
        <label />
        <address>447712345678</address>
        <alias>@esendex</alias>
        <type>Professional</type>
        <messagesremaining>1000</messagesremaining>
        <expireson>2020-01-01T00:00:00</expireson>
        <role>PowerUser</role>
        <defaultdialcode>44</defaultdialcode>
        <settings uri="https://api.esendex.com/v1.0/accounts/33efe83d-f1ac-44d9-929f-2c65c937aad3/settings" />
    </account>
</accounts>
XML;

    private $reference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $service;

    function setUp()
    {
        $this->reference = "EX123456";
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

        $this->service = new CheckAccessService($this->httpUtil);
    }

    /**
     * @test
     */
    function checkAccess()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/accounts"
            ),
            $this->isInstanceOf("\\Esendex\\Authentication\\LoginAuthentication")
        )
            ->will($this->returnValue(self::ACCOUNTS_RESPONSE_XML));

        $result = $this->service->checkAccess($this->reference, $this->username, $this->password);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    function checkAccessInputCaseMismatchWithResponseCase()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/accounts"
            ),
            $this->isInstanceOf("\\Esendex\\Authentication\\LoginAuthentication")
        )
            ->will($this->returnValue(self::ACCOUNTS_RESPONSE_XML));

        $result = $this->service->checkAccess(strtolower($this->reference), $this->username, $this->password);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    function checkAccessAccountReferenceNotAccessible()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->will($this->returnValue(self::ACCOUNTS_RESPONSE_XML));

        $result = $this->service->checkAccess("Wrong", $this->username, $this->password);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    function checkSessionAccess()
    {
        $session = new Authentication\SessionAuthentication($this->reference, uniqid());
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->anything(),
            $this->equalTo($session)
        )
            ->will($this->returnValue(self::ACCOUNTS_RESPONSE_XML));

        $result = $this->service->checkSessionAccess($session);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    function checkAuthenticationAccess()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->anything(),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue(self::ACCOUNTS_RESPONSE_XML));

        $result = $this->service->checkAuthenticationAccess($this->authentication);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    function checkAuthenticationAccessUnexpectedResponse()
    {
        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->will($this->throwException(new \Exception()));

        $result = $this->service->checkAuthenticationAccess($this->authentication);

        $this->assertFalse($result);
    }
}

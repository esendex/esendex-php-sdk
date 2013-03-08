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

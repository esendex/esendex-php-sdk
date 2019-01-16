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

class SentMessagesServiceTest extends \PHPUnit_Framework_TestCase
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

        $this->parser = $this->getMockBuilder("\\Esendex\\Parser\\SentMessagesXmlParser")
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new SentMessagesService($this->authentication, $this->httpUtil, $this->parser);
    }

    /**
     * @test
     */
    function latestReturnsSentMessagesPage()
    {
        $response = "xml response";
        $sentMessagesPage = new Model\SentMessagesPage(0, 10);

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/messageheaders?accountreference={$this->reference}"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue($response));
        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue($sentMessagesPage));

        $result = $this->service->latest();

        $this->assertSame($sentMessagesPage, $result);
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
                "https://api.esendex.com/v1.0/messageheaders?startIndex={$startIndex}&accountreference={$this->reference}"
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
                "https://api.esendex.com/v1.0/messageheaders?startIndex={$startIndex}&count={$count}&accountreference={$this->reference}"
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
                "https://api.esendex.com/v1.0/messageheaders?count={$count}&accountreference={$this->reference}"
            ),
            $this->equalTo($this->authentication)
        );

        $this->service->latest(null, $count);
    }

    /**
     * @test
     */
    function loadMessagesWithStartAndFinishReturnsSentMessagesPage()
    {
        $start = new \DateTime();
        $start->sub(new \DateInterval('P1M'));
        $startFormatted = rawurlencode($start->format(\DateTime::ISO8601));
        $finish = new \DateTime();
        $finishFormatted = rawurlencode($finish->format(\DateTime::ISO8601));
        $response = "xml response";
        $sentMessagesPage = new Model\SentMessagesPage(0, 10);

        $this->httpUtil
            ->expects($this->once())
            ->method("get")
            ->with(
            $this->equalTo(
                "https://api.esendex.com/v1.0/messageheaders?" .
                "start={$startFormatted}&" .
                "finish={$finishFormatted}&" .
                "accountreference={$this->reference}"
            ),
            $this->equalTo($this->authentication)
        )
            ->will($this->returnValue($response));

        $this->parser
            ->expects($this->once())
            ->method("parse")
            ->with($this->equalTo($response))
            ->will($this->returnValue($sentMessagesPage));

        $options = array('start' => $start, 'finish' => $finish);
        $result = $this->service->loadMessages($options);

        $this->assertSame($sentMessagesPage, $result);
    }
}

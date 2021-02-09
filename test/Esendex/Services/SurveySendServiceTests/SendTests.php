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

class SendTests extends  \PHPUnit\Framework\TestCase
{
    private $reference;
    private $username;
    private $password;
    private $authentication;
    private $httpUtil;
    private $service;

    function setUp() : void
    {
        $this->reference = "EX123456";
        $this->username = "jhdkfjh";
        $this->password = "dklfjlsdjkf";
        $this->authentication = new Authentication\LoginAuthentication(
            $this->reference,
            $this->username,
            $this->password
        );

        $this->httpUtil = $this->getMockForAbstractClass("\\Esendex\\Http\\IHttp");
        $this->httpUtil->expects($this->any())
            ->method("isSecure")
            ->will($this->returnValue(""));

        $this->service = new SurveySendService($this->authentication, $this->httpUtil);
    }

    /**
     * @test
     */
    function sendSurvey()
    {
        $surveyId = "586918CE-FC15-4204-9701-7DE5F975F9D6";
        $recipient = "447712345678";
        $this->httpUtil
            ->expects($this->once())
            ->method("postJson")
            ->with(
              $this->equalTo(
                  "https://surveys.api.esendex.com/v1.0/surveys/{$surveyId}/send"
              ),
              $this->equalTo($this->authentication),
              $this->equalTo("{\"recipients\":[{\"phonenumber\":\"{$recipient}\",\"templatefields\":null,\"metadata\":null}]}")
            )
            ->will($this->returnValue(true));

        $this->service->send($surveyId, $recipient);
    }
}

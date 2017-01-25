<?php
/**
 * Copyright (c) 2016, Esendex Ltd.
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
 * @category   Service
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2016 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex;

use Esendex\Model\Surveys\DateRangeType;

class SurveyReportService
{
    private $authentication;
    private $httpClient;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     * @param Parser\SurveyReportXmlParser $parser
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\SurveyReportXmlParser $parser = null
    ) {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);

        $this->parser = (isset($parser))
            ? $parser
            : new Parser\SurveyReportXmlParser();
    }

    /**
     * @param string $surveyId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $dateRangeType
     * @return Model\Surveys\StandardReport
     */
    public function getStandardReport($surveyId, $startDate = null, $endDate = null, $dateRangeType = DateRangeType::QuestionSent)
    {
        $url = $this->getStandardReportUrl($surveyId, $startDate, $endDate, $dateRangeType);

        $xmlResult = $this->httpClient->get($url, $this->authentication);

        return $this->parser->parse($xmlResult);
    }

    private function getStandardReportUrl($surveyId, $startDate, $endDate, $dateRangeType) {
        $uri = "https://surveys.api.esendex.com/v1.0/surveys/{$surveyId}/report/standard";
        $queryData = array();

        if($dateRangeType == DateRangeType::QuestionSent)
        {
            if($startDate != null) {
                $queryData["questionSentAfter"] = $startDate->format('Y-m-d\TH:i:s');
            }

            if($endDate != null) {
                $queryData["questionSentBefore"] = $endDate->format('Y-m-d\TH:i:s');
            }
        }
        else if ($dateRangeType == DateRangeType::AnswerReceived)
        {
            if($startDate != null) {
                $queryData["answerReceivedAfter"] = $startDate->format('Y-m-d\TH:i:s');
            }

            if($endDate != null) {
                $queryData["answerReceivedBefore"] = $endDate->format('Y-m-d\TH:i:s');
            }
        }
        else
        {
            throw new \Exception("Invalid date range type");
        }

        $queryString = http_build_query($queryData);

        return $queryString === "" ? $uri : $uri . "?" . $queryString;
    }
}

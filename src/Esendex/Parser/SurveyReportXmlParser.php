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
 * @category   Parser
 * @package    Esendex
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Parser;

use Esendex\Model\Surveys\StandardReport;
use Esendex\Model\Surveys\StandardReportRow;

class SurveyReportXmlParser
{
    public function parse($xml)
    {
        $response = simplexml_load_string($xml);

        $standardReportRows = array();
        foreach($response->row as $row)
        {
            $parsedReportRow = $this->parseStandardReportRow($row);
            $standardReportRows[] = $parsedReportRow;
        }

        return new StandardReport($standardReportRows);
    }

    private function parseStandardReportRow($reportRow) {
        $standardReportRow = new StandardReportRow();
        $standardReportRow->recipient($reportRow->recipient);
        $standardReportRow->status($reportRow->status);
        $standardReportRow->questionLabel($reportRow->questionlabel);
        $standardReportRow->questionDateTime($this->parseDateTime($reportRow->questiondatetime));
        $standardReportRow->answerLabel($reportRow->answerlabel);
        $standardReportRow->answerDateTime($this->parseDateTime($reportRow->answerdatetime));
        $standardReportRow->answerText($reportRow->answertext);
        $standardReportRow->recipientData($this->parseRecipientData($reportRow->recipientdata));

        return $standardReportRow;
    }

    private function parseDateTime($value)
    {
        return \DateTime::createFromFormat('Y-m-d\TH:i:s.u', $value);
    }

    private function parseRecipientData($data) {
        $recipientData = array();
        foreach($data->recipientdataitem as $recipientDataItem) {
            $recipientData[(string)$recipientDataItem->key] = (string)$recipientDataItem->value;
        }
        return $recipientData;
    }
}

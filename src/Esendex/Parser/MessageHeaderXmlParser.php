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
 * @category   Parser
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2013 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Parser;
use Esendex\Model\Message;
use Esendex\Model\SentMessage;
use Esendex\Model\InboxMessage;

class MessageHeaderXmlParser
{
    public function parse($xml)
    {
        $header = simplexml_load_string($xml);
        return $this->parseHeader($header);
    }

    public function parseHeader($header)
    {
        $direction = $header->direction;
        $result = ($direction == Message::Inbound)
            ? new InboxMessage()
            : new SentMessage();

        $result->id($header["id"]);
        $result->originator($header->from->phonenumber);
        $result->recipient($header->to->phonenumber);
        $result->status($header->status);
        $result->type($header->type);
        $result->direction($direction);
        $result->parts($header->parts);
        $result->bodyUri($header->body["uri"]);
        $result->summary($header->summary);
        $result->lastStatusAt($this->ParseDateTime($header->laststatusat));
        if ($direction == Message::Outbound) {
            $result->submittedAt($this->ParseDateTime($header->submittedat));
            $result->sentAt($this->ParseDateTime($header->sentat));
            $result->deliveredAt($this->ParseDateTime($header->deliveredat));
            $result->username($header->username);
        } else {
            $result->receivedAt($this->ParseDateTime($header->receivedat));
            $readAt = $header->readat;
            if (substr($readAt, 0, 2) != "00") {
                $result->readAt($this->ParseDateTime($readAt));
                $result->readBy($header->readby);
            }
        }

        return $result;
    }
	
    private function ParseDateTime($value)
    {
		$value = (strlen($value) > 20)
			? substr($value, 0, 19) . "Z"
			: $value;
			
        return \DateTime::createFromFormat(DATE_ISO8601, $value);
    }
}

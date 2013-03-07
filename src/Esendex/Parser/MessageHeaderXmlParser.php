<?php
namespace Esendex\Parser;
use Esendex\Model\Message;
use Esendex\Model\SentMessage;
use Esendex\Model\InboxMessage;

class MessageHeaderXmlParser
{
    const DATE_ISO8601_MILLISECONDS = "Y-m-d\TH:i:s.uO";

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
        return (strlen($value) > 20)
            ? \DateTime::createFromFormat(self::DATE_ISO8601_MILLISECONDS, $value)
            : \DateTime::createFromFormat(DATE_ISO8601, $value);
    }
}

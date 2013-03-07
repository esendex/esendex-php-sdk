<?php
namespace Esendex\Parser;
use Esendex\Model\Api;
use Esendex\Model\Message;
use Esendex\Model\ResultItem;
use Esendex\Exceptions\ArgumentException;
use Esendex\Exceptions\XmlException;

class DispatchXmlParser
{
    private $reference;

    function __construct($accountReference)
    {
        $this->reference = $accountReference;
    }

    public function encode(\Esendex\Model\DispatchMessage $message)
    {
        if (strlen($message->originator()) < 1)
            throw new ArgumentException("Originator is invalid");
        if (strlen($message->recipient()) < 1)
            throw new ArgumentException("Recipient is invalid");
        if ($message->validityPeriod() > 72)
            throw new ArgumentException("Validity too long, must be less or equal to than 72");

        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><messages />", 0, false, Api::NS);
        $doc->addChild("accountreference", $this->reference);
        $child = $doc->addChild("message");
        $child->addChild("from", $message->originator());
        $child->addChild("to", $message->recipient());
        $child->addChild("body", $message->body());
        $child->addChild("type", Message::SmsType);
        if ($message->validityPeriod() > 0)
            $child->addChild("validity", $message->validityPeriod());
        if ($message->language() != null)
            $child->addChild("lang", $message->language());

        return $doc->asXML();
    }

    public function parse($xml)
    {
        $headers = simplexml_load_string($xml);
        if ($headers->getName() != "messageheaders")
            throw new XmlException("Xml is missing <messageheaders /> root element");

        $results = array();
        foreach ($headers->messageheader as $header)
        {
            $results[] = new ResultItem($header["id"], $header["uri"]);
        }

        return $results;
    }
}

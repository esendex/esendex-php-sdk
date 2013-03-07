<?php
namespace Esendex\Parser;

class InboxXmlParser
{
    private $headerParser;

    public function __construct(MessageHeaderXmlParser $headerParser)
    {
        if (is_null($headerParser))
            throw new \Esendex\Exceptions\ArgumentException("headerParser must be set");

        $this->headerParser = $headerParser;
    }

    public function parse($xml)
    {
        $headers = simplexml_load_string($xml);
        $result = new \Esendex\Model\InboxPage($headers["startindex"], $headers["totalcount"]);
        foreach ($headers->messageheader as $header)
        {
            $result[] = $this->headerParser->parseHeader($header);
        }
        return $result;
    }
}

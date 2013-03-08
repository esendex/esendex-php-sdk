<?php
namespace Esendex;

class InboxService
{
    const INBOX_SERVICE = 'inbox';
    const INBOX_SERVICE_VERSION = 'v1.0';

    private $authentication;
    private $httpClient;
    private $parser;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     * @param Parser\InboxXmlParser $parser
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\InboxXmlParser $parser = null
    ) {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
        $this->parser = (isset($parser))
            ? $parser
            : new Parser\InboxXmlParser(new Parser\MessageHeaderXmlParser());
    }

    /**
     * @param int $startIndex
     * @param int $count
     * @return array
     */
    public function latest($startIndex = null, $count = null)
    {
        $uri = Http\UriBuilder::serviceUri(
            self::INBOX_SERVICE_VERSION,
            self::INBOX_SERVICE,
            array($this->authentication->accountReference(), "messages"),
            $this->httpClient->isSecure()
        );

        $query = array();
        if ($startIndex != null && is_int($startIndex)) {
            $query["startIndex"] = $startIndex;
        }
        if ($count != null && is_int($count)) {
            $query["count"] = $count;
        }
        $glue = "?";
        foreach ($query as $key => $value)
        {
            $uri .= "{$glue}{$key}={$value}";
            $glue = "&";
        }

        $data = $this->httpClient->get(
            $uri,
            $this->authentication
        );

        return $this->parser->parse($data);
    }

    /**
     * Delete an inbox message using it's messageId
     *
     * @param string $messageId
     * @return bool
     */
    function deleteInboxMessage($messageId)
    {
        $uri = Http\UriBuilder::serviceUri(
            self::INBOX_SERVICE_VERSION,
            self::INBOX_SERVICE,
            array("messages", $messageId),
            $this->httpClient->isSecure()
        );

        return $this->httpClient->delete($uri, $this->authentication) == 200;
    }
}
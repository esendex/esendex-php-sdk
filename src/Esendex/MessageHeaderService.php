<?php
namespace Esendex;

class MessageHeaderService
{
    const SERVICE = "messageheaders";
    const SERVICE_VERSION = "v1.0";

    private $authentication;
    private $httpClient;
    private $parser;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     * @param Parser\MessageHeaderXmlParser $parser
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\MessageHeaderXmlParser $parser = null
    )
    {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
        $this->parser = (isset($parser))
            ? $parser
            : new Parser\MessageHeaderXmlParser();
    }

    /**
     * Get detailed information about a message from it's messageId.
     *
     * @param $messageId
     * @return Model\InboxMessage|Model\SentMessage
     */
    public function message($messageId)
    {
        $uri = Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            array($messageId),
            $this->httpClient->isSecure()
        );

        $result = $this->httpClient->get(
            $uri,
            $this->authentication
        );

        return $this->parser->parse($result);
    }
}

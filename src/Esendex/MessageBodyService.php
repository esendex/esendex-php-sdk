<?php
namespace Esendex;

class MessageBodyService
{
    const SERVICE = "messageheaders";
    const SERVICE_VERSION = "v1.0";

    private $authentication;
    private $httpClient;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     */
    function __construct(Authentication\IAuthentication $authentication, Http\IHttp $httpClient = null)
    {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
    }

    /**
     * @param $object
     * @return string
     * @throws Exceptions\ArgumentException
     */
    public function getMessageBody($object)
    {
        if ($object instanceof \Esendex\Model\ResultMessage) {
            return $this->getMessageBodyById($object->bodyUri());
        }

        if (is_string($object)) {
            return $this->loadMessageBody($object);
        }

        throw new Exceptions\ArgumentException("Should be either MessageBody Uri or ResultMessage");
    }

    /**
     * @param $messageId
     * @return string
     * @throws Exceptions\ArgumentException
     */
    public function getMessageBodyById($messageId)
    {
        if ($messageId == null) {
            throw new Exceptions\ArgumentException("messageId is null");
        }
        if (!is_string($messageId)) {
            throw new Exceptions\ArgumentException("messageId is not a string");
        }

        $uri = Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            array($messageId, "body"),
            $this->httpClient->isSecure()
        );

        return $this->loadMessageBody($uri);
    }

    private function loadMessageBody($uri)
    {
        $result = $this->httpClient->get(
            $uri,
            $this->authentication
        );

        $messageBody = simplexml_load_string($result);

        return (string)$messageBody->bodytext;
    }
}

<?php
namespace Esendex;

class DispatchService
{
    const SERVICE = "messagedispatcher";
    const SERVICE_VERSION = "v1.0";

    private $authentication;
    private $httpClient;
    private $parser;

    /**
     * @param Authentication\IAuthentication $authentication
     * @param Http\IHttp $httpClient
     * @param Parser\DispatchXmlParser $parser
     */
    public function __construct(
        Authentication\IAuthentication $authentication,
        Http\IHttp $httpClient = null,
        Parser\DispatchXmlParser $parser = null
    ) {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
        $this->parser = (isset($parser))
            ? $parser
            : new Parser\DispatchXmlParser($authentication->accountReference());
    }

    /**
     * @param Model\DispatchMessage $message
     * @return Model\ResultItem
     * @throws Exceptions\EsendexException
     */
    public function send(Model\DispatchMessage $message)
    {
        $xml = $this->parser->encode($message);
        $uri = Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            null,
            $this->httpClient->isSecure()
        );

        $result = $this->httpClient->post(
            $uri,
            $this->authentication,
            $xml
        );

        $arr = $this->parser->parse($result);

        if (count($arr) >= 1) {
            return $arr[0];
        } else {
            throw new Exceptions\EsendexException("Error parsing the dispatch result", null, array('data_returned' => $result));
        }
    }
}

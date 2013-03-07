<?php
namespace Esendex;

class SessionService
{
    const SERVICE = 'session';
    const SERVICE_VERSION = 'v1.0';

    function __construct(Http\IHttp $httpClient = null)
    {
        $this->httpUtil = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
    }

    /**
     * Retrieve a SessionAuthentication instance
     *
     * @param string $accountRef
     * @param string $username
     * @param string $password
     * @return SessionAuthentication
     */
    function startSession($accountRef, $username, $password)
    {
        $login = new Authentication\LoginAuthentication($accountRef, $username, $password);

        $uri = Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            array("constructor"),
            $this->httpUtil->isSecure()
        );

        $result = $this->httpUtil->post($uri, $login, '');
        $session = simplexml_load_string($result);

        return new Authentication\SessionAuthentication($accountRef, $session->id);
    }
}

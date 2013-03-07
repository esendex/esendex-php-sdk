<?php
namespace Esendex;

class CheckAccessService
{
    const SERVICE = "accounts";
    const SERVICE_VERSION = 'v1.0';

    private $httpClient;

    public function __construct(Http\IHttp $httpClient = null)
    {
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new Http\HttpClient(true);
    }

    /**
     * Check that your username, password and account reference is valid
     *
     * @param string $accountReference
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function checkAccess($accountReference, $username, $password)
    {
        return $this->checkAuthenticationAccess(
            new Authentication\LoginAuthentication($accountReference, $username, $password)
        );
    }

    /**
     * Check if a session is valid, a valid session ID might have timed out and have
     * been deleted.  By calling this method the session will be
     * 'kept alive'
     *
     * @param Authentication\SessionAuthentication $authentication
     * @return bool
     */
    public function checkSessionAccess(Authentication\SessionAuthentication $authentication)
    {
        return $this->checkAuthenticationAccess($authentication);
    }

    /**
     * Check that any authentication is valid
     *
     * @param Authentication\IAuthentication $authentication
     * @return bool
     */
    public function checkAuthenticationAccess(Authentication\IAuthentication $authentication)
    {
        try {
            $uri = Http\UriBuilder::serviceUri(
                self::SERVICE_VERSION,
                self::SERVICE,
                null,
                $this->httpClient->isSecure()
            );

            $xml = $this->httpClient->get($uri, $authentication);
            $accounts = new \SimpleXMLElement($xml);
            $accounts->registerXPathNamespace("api", Model\Api::NS);
            $result = $accounts->xpath("//api:account[api:reference='{$authentication->accountReference()}']");

            return count($result) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
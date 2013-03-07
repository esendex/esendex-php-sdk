<?php
namespace Esendex\Http;
use Esendex\Authentication\IAuthentication;

class HttpClient implements IHttp
{
    private static $userAgent;
    private static $certificateBundle;

    public static function init()
    {
        $hostInfo = php_uname("s") . " " . php_uname("v") . "; " . php_uname("m");
        $agent = "esendex-php-sdk/" . \Esendex\Model\Api::getVersion() . " ({$hostInfo})";
        $agent .= " PHP/" . PHP_VERSION . " (" . PHP_OS . ")";
        $curlVersion = \curl_version();
        if (isset($curlVersion["version"])) {
            $agent .= " curl/" . $curlVersion["version"];
        }
        self::$userAgent = $agent;
        self::$certificateBundle = realpath(dirname(__FILE__) . '/../../ca-bundle.pem');
    }

    private $isSecure;

    public function __construct($secure = true)
    {
        $this->isSecure = $secure;
    }

    public function isSecure($secure = null)
    {
        if (isset($secure) && is_bool($secure)) {
            $this->isSecure = $secure;
        }
        return $this->isSecure;
    }

    public function get($url, IAuthentication $authentication)
    {
        $results = $this->request($url, $authentication, 'GET');

        return $results['data'];
    }

    public function put($url, IAuthentication $authentication, $data)
    {
        $results = $this->request($url, $authentication, 'PUT', $data);

        return $results['data'];
    }

    public function post($url, IAuthentication $authentication, $data)
    {
        $results = $this->request($url, $authentication, 'POST', $data);

        return $results['data'];
    }

    public function delete($url, IAuthentication $authentication)
    {
        $results = $this->request($url, $authentication, 'DELETE');

        return $results['statuscode'];
    }

    private function request($url, $authentication, $method, $data = null)
    {
        $httpHeaders = array("Authorization: {$authentication->getEncodedValue()}");

        $curlHandle = \curl_init();

        \curl_setopt($curlHandle, CURLOPT_URL, $url);
        \curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $httpHeaders);
        \curl_setopt($curlHandle, CURLOPT_FAILONERROR, 1);
        \curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects.
        \curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        \curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        \curl_setopt($curlHandle, CURLOPT_CAINFO, self::$certificateBundle);
        \curl_setopt($curlHandle, CURLOPT_USERAGENT, self::$userAgent);
        \curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        if ($method == 'PUT' || $method == 'POST') {
            \curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
            $httpHeaders[] = 'Content-Length: ' . strlen($data);
        }

        $result = \curl_exec($curlHandle);
        $curlInfo = \curl_getinfo($curlHandle);

        $results = array();
        $results['data'] = $result;
        $results['url'] = $url;
        $results['statuscode'] = $curlInfo["http_code"];
        $results['curlerror'] = \curl_error($curlHandle);

        \curl_close($curlHandle);

        if ($results['statuscode'] != 200) {
            throw $this->getHttpException($results['statuscode'], $result['curlerror'], $curlInfo);
        }

        return $results;
    }

    private function getHttpException($http_code, $error_message = '', array $info = null)
    {
        $exception = null;

        switch ($http_code) {
            case 400:
                $exception = new BadRequestException($error_message, $http_code, $info);
                break;
            case 401:
                $exception = new UnauthorisedException($error_message, $http_code, $info);
                break;
            case 402:
                $exception = new PaymentRequiredException($error_message, $http_code, $info);
                break;
            case 403:
                $exception = new UserCredentialsException($error_message, $http_code, $info);
                break;
            case 404:
                if ($info != null && array_key_exists('url', $info)) {
                    $exception = new ResourceNotFoundException($error_message);
                } else {
                    $exception = new ResourceNotFoundException($error_message, $http_code, $info);
                }
                break;
            case 405:
                $exception = new MethodNotAllowedException($error_message, $http_code, $info);
                break;
            case 408:
                $exception = new RequestTimedOutException($error_message, $http_code, $info);
                break;
            case 500:
                $exception = new ServerErrorException($error_message, $http_code, $info);
                break;
            case 501:
                $exception = new NotImplementedException($error_message, $http_code, $info);
                break;
            case 503:
                $exception = new ServiceUnavailableException($error_message, $http_code, $info);
                break;
            default:
                echo("Returning generic Exception for http code {$http_code}");

                $exception = new \Exception($error_message, $http_code, $info);
                break;
        }

        return $exception;
    }
}

HttpClient::init();

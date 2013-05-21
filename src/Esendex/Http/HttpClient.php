<?php
/**
 * Copyright (c) 2013, Esendex Ltd.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Esendex nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Http
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2013 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
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
        if (empty(self::$certificateBundle)) {
            echo "WARN: Could not locate CA Bundle. Secure web requests will fail";
        }
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
            if (strlen($data) == 0) {
                $httpHeaders[] = 'Content-Length: 0';
            }
        }
        \curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $httpHeaders);

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
        switch ($http_code) {
            case 400:
                return new BadRequestException($error_message, $http_code, $info);
            case 401:
                return new UnauthorisedException($error_message, $http_code, $info);
            case 402:
                return new PaymentRequiredException($error_message, $http_code, $info);
            case 403:
                return new UserCredentialsException($error_message, $http_code, $info);
            case 404:
                return new ResourceNotFoundException($error_message, $http_code, $info);
            case 405:
                return new MethodNotAllowedException($error_message, $http_code, $info);
            case 408:
                return new RequestTimedOutException($error_message, $http_code, $info);
            case 500:
                return new ServerErrorException($error_message, $http_code, $info);
            case 501:
                return new NotImplementedException($error_message, $http_code, $info);
            case 503:
                return new ServiceUnavailableException($error_message, $http_code, $info);
            default:
                if (empty($error_message)) {
                    $error_message = "An unexpected error occurred processing the web request";
                }
                return new \Exception($error_message, $http_code);
        }
    }
}

HttpClient::init();

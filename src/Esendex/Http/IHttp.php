<?php
namespace Esendex\Http;
use Esendex\Authentication\IAuthentication;

interface IHttp
{
    function isSecure($secure = null);

    function get($url, IAuthentication $authentication);

    function put($url, IAuthentication $authentication, $data);

    function post($url, IAuthentication $authentication, $data);

    function delete($url, IAuthentication $authentication);
}

class HttpException extends \Esendex\Exceptions\EsendexException {}

class BadRequestException extends HttpException {}

class UnauthorisedException extends HttpException {}

class PaymentRequiredException extends HttpException {}

class UserCredentialsException extends HttpException {}

class ResourceNotFoundException extends HttpException {}

class MethodNotAllowedException extends HttpException {}

class RequestTimedOutException extends HttpException {}

class ServerErrorException extends HttpException {}

class NotImplementedException extends HttpException {}

class ServiceUnavailableException extends HttpException {}

<?php
namespace Esendex\Authentication;

class LoginAuthentication extends AbstractAuthentication
{
    private $username, $password;

    /**
     * @param $accountReference
     * @param $username
     * @param $password
     */
    public function __construct($accountReference, $username, $password)
    {
        parent::__construct($accountReference);

        $this->username = (string)$username;
        $this->password = (string)$password;
    }

    /**
     * @return string
     */
    function getEncodedValue()
    {
        return "Basic " . base64_encode("{$this->username}:{$this->password}");
    }
}
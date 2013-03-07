<?php
namespace Esendex\Authentication;

class SessionAuthentication extends AbstractAuthentication
{
    private $sessionId;

    /**
     * @param $accountReference
     * @param $sessionId
     */
    public function __construct($accountReference, $sessionId)
    {
        parent::__construct($accountReference);

        $this->sessionId = (string)$sessionId;
    }

    /**
     * @return string
     */
    public function getEncodedValue()
    {
        return "Basic " . base64_encode("{$this->sessionId}");
    }
}
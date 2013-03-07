<?php
namespace Esendex\Authentication;

abstract class AbstractAuthentication implements IAuthentication
{
    private $accountReference;

    /**
     * @param $accountReference
     */
    protected function __construct($accountReference)
    {
        $this->accountReference = (string)$accountReference;
    }

    /**
     * @return string
     */
    public function accountReference()
    {
        return $this->accountReference;
    }

    /**
     * @return string
     */
    public abstract function getEncodedValue();
}
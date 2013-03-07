<?php
namespace Esendex\Exceptions;

class EsendexException extends \Exception
{
    private $exceptionInfo;

    public function __construct($message = '', $code = null, array $_info = null)
    {
        parent::__construct($message, $code);

        $this->exceptionInfo($_info);
    }

    public function exceptionInfo($exceptionInfo = null)
    {
        if ($exceptionInfo != null) {
            $this->exceptionInfo = $exceptionInfo;
        }
        return $this->exceptionInfo;
    }

    public function __toString()
    {
        $error = array(
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'stacktrace' => substr($this->getTraceAsString(), 0, 400),
        );

        if ($this->exceptionInfo != null) {
            array_merge($error, $this->exceptionInfo());
        }

        return print_r($error, true);
    }
}

<?php
namespace Esendex\Exceptions;

/**
 * XmlException is thrown when an error occurs when parsing XML. It could mean
 * the XML was invalid or not in the format expected.
 */
class XmlException extends EsendexException
{
    protected $xml;

    /**
     * @param string $message
     * @param int $code
     * @param string $xml, The XML string that failed parsing
     */
    public function __construct($message, $code = 0, $xml = '')
    {
        parent::__construct($message, $code);

        $this->xml = htmlspecialchars($xml, ENT_QUOTES);
    }

    public function __toString()
    {
        return parent::__toString() . "XML:\n{$this->xml}";
    }
}

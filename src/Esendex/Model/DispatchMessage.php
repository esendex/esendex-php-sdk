<?php
namespace Esendex\Model;

class DispatchMessage extends Message
{
    const ENGLISH_LANGUAGE = "en-gb";

    private $validityPeriod;
    private $body;
    private $language;

    /**
     * @param string $originator
     * @param string $recipient
     * @param string $body
     * @param string $type
     * @param int $validityPeriod
     * @param string $language
     */
    public function __construct(
        $originator,
        $recipient,
        $body,
        $type,
        $validityPeriod = 0,
        $language = self::ENGLISH_LANGUAGE
    ) {
        $this->originator($originator);
        $this->recipient($recipient);
        $this->body($body);
        $this->type($type);
        $this->validityperiod($validityPeriod);
        $this->language($language);
    }

    /**
     * @param int $value
     * @return int
     */
    public function validityPeriod($value = null)
    {
        if ($value != null) {
            $this->validityPeriod = (int)$value;
        }
        return $this->validityPeriod;
    }

    /**
     * @param string $value
     * @return string
     */
    public function body($value = null)
    {
        if ($value != null) {
            $this->body = (string)$value;
        }
        return $this->body;
    }

    /**
     * If the type of message is Voice then the language of the message
     * can be set so it will be read out to the user in a native way.
     *
     * @param string $value
     * @return string
     */
    public function language($value = null)
    {
        if ($value != null) {
            $this->language = (string)$value;
        }
        return $this->language;
    }
}

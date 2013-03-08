<?php
namespace Esendex\Model;

use Esendex\Exceptions\ArgumentException;

abstract class Message
{
    const Inbound = 'Inbound';
    const Outbound = 'Outbound';

    const SmsType = "SMS";
    const VoiceType = "Voice";

    private $originator;
    private $recipient;
    private $status;
    private $type;

    /**
     * @param string $value
     * @return string
     */
    public function originator($value = null)
    {
        if ($value != null) {
            $this->originator = (string)$value;
        }
        return $this->originator;
    }

    /**
     * @param string $value
     * @return string
     * @throws \Esendex\Exceptions\ArgumentException
     */
    public function recipient($value = null)
    {
        if ($value != null) {
            if (strlen($value) == 0) {
                throw new ArgumentException('The recipient given to this message is empty');
            }
            $this->recipient = (string)$value;
        }
        return $this->recipient;
    }

    /**
     * @param string $value
     * @return string
     */
    public function status($value = null)
    {
        if ($value != null) {
            $this->status = (string)$value;
        }
        return $this->status;
    }

    /**
     * @param string $value
     * @return string
     * @throws \Esendex\Exceptions\ArgumentException
     */
    public function type($value = null)
    {
        if ($value != null) {
            if ($value != self::SmsType && $value != self::VoiceType) {
                throw new ArgumentException("type() value was '{$value}' and must be either 'SMS' or 'Voice'");
            }
            $this->type = (string)$value;
        }
        return $this->type;
    }
}

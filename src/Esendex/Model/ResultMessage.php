<?php
namespace Esendex\Model;

abstract class ResultMessage extends Message
{
    private $id;
    private $direction;
    private $parts;
    private $bodyUri;
    private $summary;
    private $lastStatusAt;

    /**
     * @param string $value
     * @return string
     */
    public function id($value = null)
    {
        if ($value != null) {
            $this->id = (string)$value;
        }
        return $this->id;
    }

    /**
     * @param string $value
     * @return string
     */
    public function direction($value = null)
    {
        if ($value != null) {
            if ($value == self::Inbound || $value == self::Outbound) {
                $this->direction = (string)$value;
            }
        }
        return $this->direction;
    }

    /**
     * @param int $parts
     * @return int
     */
    public function parts($parts = null)
    {
        if ($parts != null) {
            $this->parts = (int)$parts;
        }
        return $this->parts;
    }

    /**
     * @param string $value
     * @return string
     */
    public function bodyUri($value = null)
    {
        if ($value != null) {
            $this->bodyUri = (string)$value;
        }
        return $this->bodyUri;
    }

    /**
     * @param string $value
     * @return string
     */
    public function summary($value = null)
    {
        if ($value != null) {
            $this->summary = (string)$value;
        }
        return $this->summary;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function lastStatusAt($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->lastStatusAt = $value;
        }
        return $this->lastStatusAt;
    }
}

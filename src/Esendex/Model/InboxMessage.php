<?php
namespace Esendex\Model;

class InboxMessage extends ResultMessage
{
    private $receivedAt;
    private $readAt;
    private $readBy;

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function receivedAt($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->receivedAt = $value;
        }
        return $this->receivedAt;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function readAt($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->readAt = $value;
        }
        return $this->readAt;
    }

    /**
     * @param string $value
     * @return string
     */
    public function readBy($value = null)
    {
        if ($value != null) {
            $this->readBy = (string)$value;
        }
        return $this->readBy;
    }
}

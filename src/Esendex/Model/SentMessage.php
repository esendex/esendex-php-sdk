<?php
namespace Esendex\Model;

class SentMessage extends ResultMessage
{
    private $submittedAt;
    private $sentAt;
    private $deliveredAt;
    private $username;

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function submittedAt($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->submittedAt = $value;
        }
        return $this->submittedAt;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function sentAt($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->sentAt = $value;
        }
        return $this->sentAt;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function deliveredAt($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->deliveredAt = $value;
        }
        return $this->deliveredAt;
    }

    /**
     * @param string $value
     * @return string
     */
    public function username($value = null)
    {
        if ($value != null) {
            $this->username = (string)$value;
        }
        return $this->username;
    }
}

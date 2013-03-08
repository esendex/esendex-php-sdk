<?php
namespace Esendex\Model;

class InboxPage implements \ArrayAccess, \Countable, \Iterator
{
    private $startIndex;
    private $totalCount;
    private $messages;
    private $position = 0;

    /**
     * @param $startIndex
     * @param $totalCount
     */
    public function __construct($startIndex, $totalCount)
    {
        $this->startIndex = (int)$startIndex;
        $this->totalCount = (int)$totalCount;
        $this->messages = array();
    }

    /**
     * @return int
     */
    public function startIndex()
    {
        return $this->startIndex;
    }

    /**
     * @return int
     */
    public function totalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     */
    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]);
    }

    /**
     * @param int $offset <p>
     * The offset to retrieve.
     * </p>
     * @return InboxMessage
     */
    public function offsetGet($offset)
    {
        return isset($this->messages[$offset])
            ? $this->messages[$offset]
            : null;
    }

    /**
     * @param int $offset <p>
     * The offset to assign the value to. Cannot be explicitly set.
     * </p>
     * @param InboxMessage $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            throw new \Esendex\Exceptions\ArgumentException("InboxPage does not support explicitly set offsets");
        }

        $this->messages[] = $value;
    }

    /**
     * @param int $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->messages[$offset]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * @return InboxMessage
     */
    public function current()
    {
        return $this->offsetGet($this->position);
    }

    /**
     * @return void
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return boolean true on success or false on failure.
     */
    public function valid()
    {
        return $this->offsetExists($this->position);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }
}

<?php
/**
 * Copyright (c) 2013, Esendex Ltd.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Esendex nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Model
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2013 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Model;

class SentMessagesPage implements \ArrayAccess, \Countable, \Iterator
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
     * @return SentMessage
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
     * @param SentMessage $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            throw new \Esendex\Exceptions\ArgumentException("SentMessagesPage does not support explicitly set offsets");
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
     * @return SentMessage
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

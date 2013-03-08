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

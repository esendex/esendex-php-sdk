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

class Account
{
    private $id;
    private $reference;
    private $label;
    private $alias;
    private $address;
    private $type;
    private $messagesRemaining;
    private $expiresOn;
    private $defaultDialCode;
    
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
    public function reference($value = null)
    {
        if ($value != null) {
            $this->reference = (string)$value;
        }
        return $this->reference;
    }

    /**
     * @param string $value
     * @return string
     */
    public function label($value = null)
    {
        if ($value != null) {
            $this->label = (string)$value;
        }
        return $this->label;
    }

    /**
     * @param string $value
     * @return string
     */
    public function alias($value = null)
    {
        if ($value != null) {
            $this->alias = (string)$value;
        }
        return $this->alias;
    }

    /**
     * @param string $value
     * @return string
     */
    public function address($value = null)
    {
        if ($value != null) {
            $this->address = (string)$value;
        }
        return $this->address;
    }

    /**
     * @param string $value
     * @return string
     */
    public function type($value = null)
    {
        if ($value != null) {
            $this->type = (string)$value;
        }
        return $this->type;
    }

    /**
     * @param int $value
     * @return int
     */
    public function messagesRemaining($value = null)
    {
        if ($value != null) {
            $this->messagesRemaining = (int)$value;
        }
        return $this->messagesRemaining;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function expiresOn($value = null)
    {
        if ($value instanceof \DateTime) {
            $this->expiresOn = $value;
        }
        return $this->expiresOn;
    }

    /**
     * @param string $value
     * @return string
     */
    public function defaultDialCode($value = null)
    {
        if ($value != null) {
            $this->defaultDialCode = (string)$value;
        }
        return $this->defaultDialCode;
    }
}

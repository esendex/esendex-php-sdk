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

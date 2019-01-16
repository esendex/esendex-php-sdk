<?php
/**
 * Copyright (c) 2019, Commify Ltd.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Commify nor the
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
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Model;
use Esendex\Exceptions\ArgumentException;
use Esendex\Model\MessageBody;

class DispatchMessage extends Message
{
    const ENGLISH_LANGUAGE = "en-GB";

    private $validityPeriod;
    private $body;
    private $language;
    private $characterSet;
    private $retries;

    /**
     * @param string $originator
     * @param string $recipient
     * @param string $body
     * @param string $type
     * @param int $validityPeriod
     * @param string $language
     * @param int $retries
     */
    public function __construct(
        $originator,
        $recipient,
        $body,
        $type,
        $validityPeriod = 0,
        $language = self::ENGLISH_LANGUAGE,
        $characterSet = null,
        $retries = null
    ) {
        $this->originator($originator);
        $this->recipient($recipient);
        $this->body($body);
        $this->type($type);
        $this->validityperiod($validityPeriod);
        $this->language($language);
        $this->characterSet($characterSet);
        $this->retries($retries);
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

     /**
     * If the type of message is Voice then the number of times to retry
     * the message if the recipient does not answer can be set.
     *
     * @param string $value
     * @return string
     */
    public function retries($value = null)
    {
        if ($value != null) {
            $this->retries = (int)$value;
        }
        return $this->retries;
    }

    /**
     * See http://developers.esendex.com/APIs/REST-API/messagedispatcher for
     * details of usage.
     *
     * @param string $value
     * @return string
     */
    public function characterSet($value = null)
    {
        if ($value != null) {
            if ($value != MessageBody::CharsetGSM 
             && $value != MessageBody::CharsetUnicode
             && $value != MessageBody::CharsetAuto) {
                throw new ArgumentException("characterSet() value was '{$value}' and must be one of '" . MessageBody::CharsetGSM . "', '" . MessageBody::CharsetUnicode . "' or '" . MessageBody::CharsetAuto . "'");
            }
            $this->characterSet = (string)$value;
        }
        return $this->characterSet;
    }
}

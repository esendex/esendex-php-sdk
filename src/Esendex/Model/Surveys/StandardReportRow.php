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
namespace Esendex\Model\Surveys;

class StandardReportRow
{
    private $recipient;
    private $status;
    private $questionLabel;
    private $questionDateTime;
    private $answerLabel;
    private $answerDateTime;
    private $answerText;
    private $recipientData = array();

    /**
     * @param string $value
     * @return string
     */
    public function recipient($value = null)
    {
        if ($value != null) {
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
     */
    public function questionLabel($value = null)
    {
        if ($value != null) {
            $this->questionLabel = (string)$value;
        }
        return $this->questionLabel;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function questionDateTime($value = null)
    {
        if ($value != null) {
            $this->questionDateTime = $value;
        }
        return $this->questionDateTime;
    }

    /**
     * @param string $value
     * @return string
     */
    public function answerLabel($value = null)
    {
        if ($value != null) {
            $this->answerLabel = (string)$value;
        }
        return $this->answerLabel;
    }

    /**
     * @param \DateTime $value
     * @return \DateTime
     */
    public function answerDateTime($value = null)
    {
        if ($value != null) {
            $this->answerDateTime = $value;
        }
        return $this->answerDateTime;
    }

    /**
     * @param string $value
     * @return string
     */
    public function answerText($value = null)
    {
        if ($value != null) {
            $this->answerText = (string)$value;
        }
        return $this->answerText;
    }

    /**
     * @param array $value
     * @return array
     */
    public function recipientData($value = null)
    {
        if ($value != null) {
            $this->recipientData = $value;
        }
        return $this->recipientData;
    }
}
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
 * @category   Testing
 * @package    Esendex
 * @author     Esendex Support <support@esendex.com>
 * @copyright  2013 Esendex Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Parser;

use Esendex\Model\Account;

class AccountXmlParserTest extends \PHPUnit_Framework_TestCase
{
    const RESPONSE_XML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<accounts xmlns="http://api.esendex.com/ns/">
    <account id="597e91b3-cf71-4950-8b4f-8728fe13793f"
             uri="https://api.esendex.com/v1.0/accounts/597e91b3-cf71-4950-8b4f-8728fe13793f">
        <reference>EX998877</reference>
        <label>Primary</label>
        <address>4477123456789</address>
        <alias>Sales</alias>
        <type>Professional</type>
        <messagesremaining>19</messagesremaining>
        <expireson>2099-09-04T00:00:00</expireson>
        <role>PowerUser</role>
        <defaultdialcode>34</defaultdialcode>
        <settings uri="https://api.esendex.com/v1.0/accounts/597e91b3-cf71-4950-8b4f-8728fe13793f/settings" />
    </account>
    <account id="a25259e0-2433-4058-8906-99d1b79517bd"
             uri="http://api.dev.esendex.com/v1.0/accounts/a25259e0-2433-4058-8906-99d1b79517bd">
        <reference>EX778899</reference>
        <label />
        <address />
        <alias>WebSMS</alias>
        <type>Broadcast</type>
        <messagesremaining>0</messagesremaining>
        <expireson>2016-06-30T13:25:15</expireson>
        <role>PowerUser</role>
        <defaultdialcode>44</defaultdialcode>
        <settings uri="http://api.dev.esendex.com/v1.0/accounts/a25259e0-2433-4058-8906-99d1b79517bd/settings" />
    </account>
</accounts>
XML;

    /**
     * @test
     */
    function parseAccountsResponse()
    {
        $parser = new AccountXmlParser();

        $result = $parser->parse(self::RESPONSE_XML);

        $this->assertContainsOnlyInstancesOf("\\Esendex\\Model\\Account", $result);
        $this->assertCount(2, $result);

        $this->assertEquals("597e91b3-cf71-4950-8b4f-8728fe13793f", $result[0]->id());
        $this->assertEquals("EX998877", $result[0]->reference());
        $this->assertEquals("Primary", $result[0]->label());
        $this->assertEquals("4477123456789", $result[0]->address());
        $this->assertEquals("Sales", $result[0]->alias());
        $this->assertEquals("Professional", $result[0]->type());
        $this->assertEquals(19, $result[0]->messagesRemaining());
        $this->assertEquals(
            \DateTime::createFromFormat(\DateTime::ISO8601, "2099-09-04T00:00:00Z"),
            $result[0]->expiresOn()
        );
        $this->assertEquals("34", $result[0]->defaultDialCode());
        
        $this->assertEquals("a25259e0-2433-4058-8906-99d1b79517bd", $result[1]->id());
        $this->assertEquals("EX778899", $result[1]->reference());
        $this->assertEquals(null, $result[1]->label());
        $this->assertEquals(null, $result[1]->address());
        $this->assertEquals("WebSMS", $result[1]->alias());
        $this->assertEquals("Broadcast", $result[1]->type());
        $this->assertEquals(0, $result[1]->messagesRemaining());
        $this->assertEquals(
            \DateTime::createFromFormat(\DateTime::ISO8601, "2016-06-30T13:25:15Z"),
            $result[1]->expiresOn()
        );
        $this->assertEquals("44", $result[1]->defaultDialCode());
    }
}

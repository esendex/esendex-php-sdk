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
 * @category   Testing
 * @package    Esendex
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
namespace Esendex\Http;

class UriBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function serviceUriReturnsExpectedUri()
    {
        $version = "v1.0";
        $resource = "banana";
        $expected = "https://" . UriBuilder::HOST . "/{$version}/{$resource}";

        $result = UriBuilder::serviceUri($version, $resource);

        $this->assertEquals($expected, $result);
    }
	
	/**
     * @test
	 * @runInSeparateProcess
     */
    function serviceUriWithOverrideHostReturnsExpectedUri()
    {
		define("ESENDEX_API_HOST", "api.myfakeesendex.com");
		
        $version = "v1.0";
        $resource = "apple";
        $expected = "https://" . ESENDEX_API_HOST . "/{$version}/{$resource}";

        $result = UriBuilder::serviceUri($version, $resource);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function serviceUriReturnsExpectedUriWhenUnsecured()
    {
        $version = "v1.0";
        $resource = "orange";
        $expected = "http://" . UriBuilder::HOST . "/{$version}/{$resource}";

        $result = UriBuilder::serviceUri($version, $resource, null, false);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function serviceUriReturnsExpectedUriWithIdentifier()
    {
        $version = "v1.0";
        $resource = "pear";
        $identifier = uniqid();
        $expected = "https://" . UriBuilder::HOST . "/{$version}/{$resource}/{$identifier}";

        $result = UriBuilder::serviceUri($version, $resource, array($identifier));

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function serviceUriReturnsExpectedUriWithIdentifierAndSubResource()
    {
        $version = "v1.0";
        $resource = "plum";
        $identifier = uniqid();
        $subResource = "peel";
        $expected = "https://" . UriBuilder::HOST . "/{$version}/{$resource}/{$identifier}/{$subResource}";

        $result = UriBuilder::serviceUri($version, $resource, array($identifier, $subResource));

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function serviceUriReturnsExpectedUriWithIdentifierRequiringEncoding()
    {
        $version = "v1.0";
        $resource = "fig";
        $identifier = "!encode me!";
        $encoded = rawurlencode($identifier);
        $expected = "https://" . UriBuilder::HOST . "/{$version}/{$resource}/{$encoded}";

        $result = UriBuilder::serviceUri($version, $resource, array($identifier));

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function buildQueryReturnsExpectedQueryString()
    {
        $params = array(
            'startIndex'=>20,
            'count'=>10
            );
        $expected = "startIndex=20&count=10";

        $result = UriBuilder::buildQuery($params);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function buildQueryWithNonUriCharsInValuesReturnsExpectedQueryString()
    {
        $params = array(
            'first'=>'value to encode',
            'second'=>'value:to/encode'
            );
        $expected = "first=value%20to%20encode&second=value%3Ato%2Fencode";

        $result = UriBuilder::buildQuery($params);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function buildQueryWithNonUriCharsInKeysReturnsExpectedQueryString()
    {
        $params = array(
            'first key'=>'value',
            'second:key'=>'value'
            );
        $expected = "first%20key=value&second%3Akey=value";

        $result = UriBuilder::buildQuery($params);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    function buildQueryWithDateTimeValueReturnsExpectedQueryString()
    {
        $params = array(
            'date'=>\DateTime::createFromFormat(\DateTime::ISO8601, '2014-03-02T01:02:03+0000')
            );
        $expected = "date=2014-03-02T01%3A02%3A03%2B0000";

        $result = UriBuilder::buildQuery($params);

        $this->assertEquals($expected, $result);
    }
}

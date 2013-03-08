<?php
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
     */
    function serviceUriReturnsExpectedUriWhenUnsecured()
    {
        $version = "v1.0";
        $resource = "banana";
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
        $resource = "banana";
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
        $resource = "banana";
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
        $resource = "banana";
        $identifier = "!encode me!";
        $encoded = rawurlencode($identifier);
        $expected = "https://" . UriBuilder::HOST . "/{$version}/{$resource}/{$encoded}";

        $result = UriBuilder::serviceUri($version, $resource, array($identifier));

        $this->assertEquals($expected, $result);
    }
}

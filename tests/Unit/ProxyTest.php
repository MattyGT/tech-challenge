<?php
namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Services\ApiRequest;
use App\Http\Services\ApiResponse;

/**
 * This is my attempt at "good" unit testing. 
 * As said in my interview, this is an area of my skills that needs a lot of work. :)
 */

class ExampleTest extends TestCase
{
    /**
     * Test for proxy endpoint not being provided
     *
     * @return void
     */
    public function testProxyNoEndpoint():void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new ApiRequest('');
    } 

    /**
     * Validate proxy API endpoint
     *
     * @return void
     */
    public function testValidateProxyEndpoint():void
    {
        $this->expectException(\Exception::class);
        
        new ApiRequest('https://api.proxyscrape.com/v2/');
    }


    /**
     * Validate proxy API endpoint with illegal chars
     *
     * @return void
     */
    public function testValidateProxyEndpointWithIllegalChars():void
    {
        $this->expectException(\Exception::class);
        
        new ApiRequest('https://£api.proxyscrape.com/v2!/?request=displayproxies&protocol=http&timeout=10000&country=GB&ssl=no&anonymity=all');
    }

    /**
     * Test for proxy URL not being provided
     *
     * @return void
     */
    public function testGetFormattedURIHeader():void
    {       
        $ApiRequest = new ApiRequest('',['1.2.3.4:80']);
        $ApiResponse = new ApiResponse($ApiRequest);

        $test = 'HTTP/1.1 200 Connection established\r\n
        \r\n
        HTTP/2 204 \r\n
        date: Tue, 01 Nov 2022 18:31:30 GMT\r\n
        content-type: text/html\r\n
        server: HTTP server (unknown)\r\n
        x-xss-protection: 0\r\n
        set-cookie: CONSENT=PENDING+651; expires=Thu, 31-Oct-2024 18:31:30 GMT; path=/; domain=.google.co.uk; Secure\r\n
        p3p: CP="This is not a P3P policy! See g.co/p3phelp for more info."\r\n
        alt-svc: h3=":443"; ma=2592000,h3-29=":443"; ma=2592000,h3-Q050=":443"; ma=2592000,h3-Q046=":443"; ma=2592000,h3-Q043=":443"; ma=2592000,quic=":443"; ma=2592000; v="46,43"\r\n
        \r\n';

        $this->assertIsArray($ApiResponse::format($test));
    }
}

<?php

namespace Tests\Functional;

class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'Enter the OTP received on' but not a greeting
     */
    public function testGetOtpPage()
    {
        $token = 'R287UT3YS7DLX856PPBQ2YFX366DVM';
        $response = $this->runApp('GET', '/token/'.$token);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Enter the OTP received on', (string)$response->getBody());
        $this->assertNotContains('Page Not Found', (string)$response->getBody());
    }

    /**
     * Test that the index route with optional name argument returns a rendered greeting
     */
    public function testGetHomepageWithGreeting()
    {
        $response = $this->runApp('GET', '/name');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello name!', (string)$response->getBody());
    }

    /**
     * Test that the index route won't accept a post request
     */
    public function testPostHomepageNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string)$response->getBody());
    }
}
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class getLastEmailsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetLastEmails()
    {
        $response = $this->json('GET', version('v1')->route('email.list', ['api_token' => env('API_KEY', '1234')]));

        $response->assertStatus(200);

        $jsonData = $response->json();

        $this->assertArrayHasKey('message', $jsonData);
        $this->assertEquals('ES query successful', $jsonData['message']);
        $this->assertArrayHasKey('meta', $jsonData);

        $this->assertArrayHasKey('data', $jsonData);
        $data = $jsonData['data'];

        foreach ($data as $item) {
            $this->assertArrayHasKey('subject', $item);
            $this->assertArrayHasKey('body', $item);
            $this->assertArrayHasKey('to', $item);

            $this->assertArrayHasKey('from', $item);
            $this->assertIsString($item['to']);
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $item['to']);
        }

        $this->assertArrayHasKey('meta', $jsonData);
        $this->assertArrayHasKey('total', $jsonData['meta']);
    }
}

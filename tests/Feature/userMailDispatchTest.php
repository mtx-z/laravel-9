<?php

namespace Tests\Feature;

use App\Jobs\ProcessUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class userMailDispatchTest extends TestCase
{
    use DatabaseTransactions;

    public function testSendWithValidationErrors()
    {
        Queue::fake();

        // Create a user and prepare invalid data
        $user = User::factory()->connection('mysql')->create();
        $invalidData = [
            [
                'subject' => 'Invalid Subject',
                'body' => 'Invalid Body',
                'email' => 'invalid_email',
            ],
        ];

        // Send a POST request to your API route with the invalid data
        $response = $this->json('POST', version('v1')->route('email.send', ['user' => $user->id, 'api_token' => config('api.key')]), $invalidData);
        Queue::assertNotPushed(ProcessUserMail::class);

        // Check that the response indicates validation errors
        $response->assertStatus(422);
        $user->delete();
    }

    public function testSendWithValidData()
    {
        Queue::fake();

        // Create a user and prepare valid data
        $user = User::factory()->connection('mysql')->create();
        $validData = [
            [
                'subject' => 'Valid Subject 1',
                'body' => 'Valid Body 1',
                'email' => 'email1@example.com',
            ],
        ];

        // Send a POST request to your API route with the valid data
        $response = $this->json('POST', version('v1')->route('email.send', ['user' => $user->id, 'api_token' => config('api.key')]), $validData);
        Queue::assertPushed(ProcessUserMail::class, count($validData));

        // Check that the response indicates success and that the job was dispatched
        $response->assertStatus(200); // 200 OK for successful request
        $response->assertJson([
            'message' => 'Emails queued successfully',
            'received' => count($validData),
            'queued' => count($validData),
            'errors' => [],
        ]);

        $this->assertIsNumeric($response['queued']);
        $this->assertLessThanOrEqual(count($validData), $response['queued']);

        $user->delete();
    }
}

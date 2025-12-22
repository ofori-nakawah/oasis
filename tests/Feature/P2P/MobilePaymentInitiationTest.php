<?php

namespace Tests\Feature\P2P;

use App\Models\JobApplication;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MobilePaymentInitiationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set Paystack secret key for testing
        config(['services.paystack.secret_key' => 'test_secret_key']);
        
        // Set P2P payment percentages
        config(['p2p.initial_payment_percentage' => 10]);
        config(['p2p.final_payment_percentage' => 90]);
    }

    /** @test */
    public function authenticated_user_can_initiate_quote_approval_payment_for_their_post()
    {
        // Arrange
        $user = User::factory()->create();
        $worker = User::factory()->create();
        
        $post = new Post();
        $post->user_id = $user->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->status = 'open';
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->status = 'applied';
        $application->save();

        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/quote_approval_123',
                'access_code' => 'access_code_123',
                'reference' => 'ref_quote_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/p2p/initiate-quote-approval-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'authorization_url',
                    'reference',
                    'access_code',
                ],
            ])
            ->assertJson([
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/quote_approval_123',
                    'reference' => 'ref_quote_123',
                ],
            ]);

        // Assert transaction was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 100.00, // 10% of 1000
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function quote_approval_payment_initiation_requires_authentication()
    {
        // Arrange
        $postOwner = User::factory()->create();
        $worker = User::factory()->create();
        
        $post = new Post();
        $post->user_id = $postOwner->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->save();

        // Act
        $response = $this->postJson('/api/v1/p2p/initiate-quote-approval-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function quote_approval_payment_initiation_validates_required_fields()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/p2p/initiate-quote-approval-payment', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['post_id', 'application_id']);
    }

    /** @test */
    public function quote_approval_payment_initiation_requires_user_owns_post()
    {
        // Arrange
        $postOwner = User::factory()->create();
        $unauthorizedUser = User::factory()->create();
        $worker = User::factory()->create();

        $post = new Post();
        $post->user_id = $postOwner->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->save();

        // Act
        $response = $this->actingAs($unauthorizedUser)->postJson('/api/v1/p2p/initiate-quote-approval-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
            ]);
    }

    /** @test */
    public function quote_approval_payment_initiation_prevents_duplicate_approvals()
    {
        // Arrange
        $user = User::factory()->create();
        $worker = User::factory()->create();

        $post = new Post();
        $post->user_id = $user->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->quote_approved_at = now();
        $application->quote_approved_by = $user->id;
        $application->status = 'confirmed';
        $application->save();

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/p2p/initiate-quote-approval-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Quote has already been approved',
            ]);
    }

    /** @test */
    public function authenticated_user_can_initiate_job_closure_payment_for_their_post()
    {
        // Arrange
        $user = User::factory()->create();
        $worker = User::factory()->create();

        $post = new Post();
        $post->user_id = $user->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->status = 'open';
        $post->payment_status = 'initial_paid';
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->status = 'confirmed';
        $application->quote_approved_at = now();
        $application->quote_approved_by = $user->id;
        $application->save();

        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/job_closure_123',
                'access_code' => 'access_code_closure_123',
                'reference' => 'ref_closure_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/p2p/initiate-job-closure-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'authorization_url',
                    'reference',
                    'access_code',
                ],
            ])
            ->assertJson([
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/job_closure_123',
                    'reference' => 'ref_closure_123',
                ],
            ]);

        // Assert transaction was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 900.00, // 90% of 1000
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function job_closure_payment_initiation_requires_authentication()
    {
        // Arrange
        $postOwner = User::factory()->create();
        $worker = User::factory()->create();
        
        $post = new Post();
        $post->user_id = $postOwner->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->save();

        // Act
        $response = $this->postJson('/api/v1/p2p/initiate-job-closure-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function job_closure_payment_initiation_validates_required_fields()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/p2p/initiate-job-closure-payment', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['post_id', 'application_id']);
    }

    /** @test */
    public function job_closure_payment_initiation_prevents_payment_for_already_closed_jobs()
    {
        // Arrange
        $user = User::factory()->create();
        $worker = User::factory()->create();

        $post = new Post();
        $post->user_id = $user->id;
        $post->type = 'P2P';
        $post->category = 'Plumbing';
        $post->description = 'Fix leaking pipe';
        $post->location = 'Accra';
        $post->date = now()->format('Y-m-d');
        $post->time = '10:00';
        $post->coords = json_encode(['lat' => 5.6, 'lng' => -0.2]);
        $post->status = 'closed';
        $post->closed_at = now();
        $post->save();

        $application = new JobApplication();
        $application->user_id = $worker->id;
        $application->post_id = $post->id;
        $application->quote = 1000.00;
        $application->save();

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/p2p/initiate-job-closure-payment', [
            'post_id' => $post->id,
            'application_id' => $application->id,
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Job is already closed',
            ]);
    }
}


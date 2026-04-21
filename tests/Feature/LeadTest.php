<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    private string $apiKey = 'test-api-key';

    private function headers(): array
    {
        return ['X-API-KEY' => $this->apiKey];
    }

    public function test_can_create_lead(): void
    {
        $payload = [
            'nombre'           => 'Juan Pérez',
            'email'            => 'juan@example.com',
            'telefono'         => '+573001234567',
            'fuente'           => 'instagram',
            'producto_interes' => 'Curso de Marketing',
            'presupuesto'      => 500,
        ];

        $response = $this->postJson('/api/leads', $payload, $this->headers());

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.email', 'juan@example.com');

        $this->assertDatabaseHas('leads', ['email' => 'juan@example.com']);
    }

    public function test_cannot_create_lead_with_duplicate_email(): void
    {
        Lead::factory()->create(['email' => 'duplicado@example.com']);

        $response = $this->postJson('/api/leads', [
            'nombre' => 'Otro Lead',
            'email'  => 'duplicado@example.com',
            'fuente' => 'facebook',
        ], $this->headers());

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    public function test_cannot_create_lead_with_invalid_fuente(): void
    {
        $response = $this->postJson('/api/leads', [
            'nombre' => 'Test Lead',
            'email'  => 'test@example.com',
            'fuente' => 'tiktok',
        ], $this->headers());

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['fuente']]);
    }

    public function test_can_list_leads_with_pagination(): void
    {
        Lead::factory(5)->create();

        $response = $this->getJson('/api/leads?page=1&limit=3', $this->headers());

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'meta', 'links'])
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_leads_by_fuente(): void
    {
        Lead::factory(3)->create(['fuente' => 'instagram']);
        Lead::factory(2)->create(['fuente' => 'facebook']);

        $response = $this->getJson('/api/leads?fuente=instagram', $this->headers());

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_get_lead_by_id(): void
    {
        $lead = Lead::factory()->create();

        $response = $this->getJson("/api/leads/{$lead->id}", $this->headers());

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $lead->id)
                 ->assertJsonPath('data.email', $lead->email);
    }

    public function test_returns_404_for_nonexistent_lead(): void
    {
        $response = $this->getJson('/api/leads/99999', $this->headers());

        $response->assertStatus(404);
    }

    public function test_can_update_lead(): void
    {
        $lead = Lead::factory()->create();

        $response = $this->patchJson("/api/leads/{$lead->id}", [
            'telefono'    => '+573009999999',
            'presupuesto' => 1500,
        ], $this->headers());

        $response->assertStatus(200)
                 ->assertJsonPath('data.telefono', '+573009999999')
                 ->assertJsonPath('data.presupuesto', 1500);
    }

    public function test_can_soft_delete_lead(): void
    {
        $lead = Lead::factory()->create();

        $this->deleteJson("/api/leads/{$lead->id}", [], $this->headers())
             ->assertStatus(200)
             ->assertJsonPath('success', true);

        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_can_get_stats(): void
    {
        Lead::factory(5)->create(['fuente' => 'instagram']);
        Lead::factory(3)->create(['fuente' => 'facebook']);

        $response = $this->getJson('/api/leads/stats', $this->headers());

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [
                     'total_leads',
                     'leads_por_fuente',
                     'promedio_presupuesto',
                     'leads_ultimos_7_dias',
                 ]]);

        $this->assertEquals(8, $response->json('data.total_leads'));
    }

    public function test_requires_api_key(): void
    {
        $response = $this->getJson('/api/leads');

        $response->assertStatus(401)
                 ->assertJsonPath('success', false);
    }

    public function test_webhook_creates_lead(): void
    {
        $payload = [
            'form_response' => [
                'answers' => [
                    ['field' => ['ref' => 'nombre'], 'text' => 'Webhook Lead'],
                    ['field' => ['ref' => 'email'], 'email' => 'webhook@example.com'],
                    ['field' => ['ref' => 'fuente'], 'text' => 'landing_page'],
                    ['field' => ['ref' => 'presupuesto'], 'number' => 750],
                ],
            ],
        ];

        $response = $this->postJson('/api/leads/webhook', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('leads', ['email' => 'webhook@example.com']);
    }
}

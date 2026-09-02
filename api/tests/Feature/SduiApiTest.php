<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SduiApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_screens_require_authentication(): void
    {
        $this->getJson('/sdui/screens/home')->assertUnauthorized();
    }

    public function test_home_screen_is_stac_json(): void
    {
        $response = $this->actingAsUser()
            ->getJson('/sdui/screens/home');

        $response
            ->assertOk()
            ->assertJsonPath('schemaVersion', 1)
            ->assertJsonPath('name', 'home')
            ->assertJsonPath('body.type', 'scaffold')
            ->assertJsonPath('body.appBar.title.data', 'Home')
            ->assertJsonPath('body.body.child.children.0.data', 'Welcome');
    }

    public function test_details_and_form_screens_exist(): void
    {
        $this->actingAsUser()
            ->getJson('/sdui/screens/details')
            ->assertOk()
            ->assertJsonPath('schemaVersion', 1)
            ->assertJsonPath('name', 'details')
            ->assertJsonPath('body.type', 'scaffold');

        $form = $this->actingAsUser()
            ->getJson('/sdui/screens/form')
            ->assertOk()
            ->assertJsonPath('body.body.child.type', 'form');

        $form->assertJsonPath('body.body.child.child.children.0.validatorRules.0.rule', 'isLength')
            ->assertJsonPath('body.body.child.child.children.0.validatorRules.0.options.min', 2)
            ->assertJsonPath('body.body.child.child.children.2.validatorRules.1.rule', 'isEmail');
    }

    public function test_unknown_screen_is_not_found(): void
    {
        $this->actingAsUser()
            ->getJson('/sdui/screens/missing')
            ->assertNotFound();
    }

    public function test_static_screens_are_cached_in_redis_key_shape(): void
    {
        Cache::flush();

        $this->actingAsUser()
            ->withHeader('Accept-Language', 'en')
            ->getJson('/sdui/screens/home')
            ->assertOk();

        $this->assertTrue(Cache::has('sdui:screen:home:en'));
        $this->assertSame('scaffold', Cache::get('sdui:screen:home:en')['type']);
    }

    public function test_profile_action_updates_the_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/sdui/actions/profile', [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Saved.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    public function test_profile_action_returns_422_for_invalid_input(): void
    {
        $this->actingAsUser()
            ->postJson('/sdui/actions/profile', [
                'name' => 'A',
                'email' => 'not-an-email',
            ])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['name', 'email']])
            ->assertJsonPath('errors.name.0', 'Enter your name');
    }

    private function actingAsUser(?User $user = null)
    {
        $user ??= User::factory()->create();

        return $this->actingAs($user, 'sanctum');
    }
}

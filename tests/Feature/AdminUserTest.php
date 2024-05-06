<?php

namespace Tests\Feature;

use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Ensure that only an admin can get a list of all users
     *
     * @test
     */
    public function only_an_admin_can_fetch_all_users(): void
    {
        User::factory(5)->create();
        $notAdmin = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => 1]);

        $this->assertGuest()->getJson('v1/admin/users')->assertStatus(401);
        $this->actingAs($notAdmin)->getJson('v1/admin/users')->assertStatus(403);
        $this->actingAs($admin)->getJson('v1/admin/users')->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'verified',
                    'admin',
                    'created',
                    'updated',
                ],
            ],
        ]);
    }

    /**
     * Ensure that only an admin can verify a user.
     *
     * @test
     */
    public function only_an_admin_can_verify_a_user(): void
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $user = User::factory()->create();

        $this->assertEquals(0, $user->is_verified);
        $this->assertGuest()->putJson("v1/admin/users/$user->name", ['verified' => 'on'])->assertStatus(401);
        $this->actingAs($user)->putJson("v1/admin/users/$user->name", ['verified' => 'on'])->assertStatus(403);
        $userResp = $this->actingAs($admin)->putJson("v1/admin/users/$user->name", ['verified' => 'on'])->assertStatus(200);
        $this->assertEquals(1, User::whereId($user->id)->first()->is_verified);
        $this->assertTrue($userResp['data']['verified']); // Also check that the api response is now "true".
    }

    /**
     * Ensure that only an admin can delete accounts using this route.
     *
     * @test
     */
    public function only_an_admin_can_delete_a_user(): void
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->deleteJson("v1/admin/users/$user->name")->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->actingAs($admin);
        $this->deleteJson("v1/admin/users/$user->name")->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}

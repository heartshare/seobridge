<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTeamTest extends TestCase
{
    public function test_user_creates_team_and_gets_activeteamid_assigned()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/auth/team/create-team', [
            'name' => 'Test Corp',
            'description' => 'Test Corp description',
            'category' => 'other',
        ]);
        
        $response->assertSuccessful();
        $this->assertEquals(User::find($user->id)->active_team_id, json_decode($response->getContent())->id);
    }
}

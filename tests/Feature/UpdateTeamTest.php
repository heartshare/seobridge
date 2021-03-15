<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_none_owner_sends_request_and_gets_403_with_text_response()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/update-team', [
            'teamId' => $team->id,
            'name' => 'Test Corp',
            'description' => 'Test Corp description',
            'category' => 'other',
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('UNAUTHORIZED', true);
    }

    public function test_user_updates_team()
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user->id,
        ]);

        TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/auth/team/update-team', [
            'teamId' => $team->id,
            'name' => 'Test Corp',
            'description' => 'Test Corp description',
            'category' => 'other',
        ]);
        
        $response->assertSuccessful();
    }
}

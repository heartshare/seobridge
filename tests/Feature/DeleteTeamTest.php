<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_none_owner_tries_to_delete_team_and_gets_403_with_text_response()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/delete-team', [
            'teamId' => $team->id,
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('UNAUTHORIZED', true);
    }

    public function test_owner_deletes_team()
    {
        $user1 = User::factory()->create();

        $team1 = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team1->id,
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/delete-team', [
            'teamId' => $team1->id,
        ]);
        
        $response->assertSuccessful();
        $this->assertEquals($team1->id, $response->getContent());
    }
}

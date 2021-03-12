<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_leaves_team_and_gets_403()
    {
        $user1 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/leave-team', [
            'id' => $team->id,
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('OWNER_CANNOT_LEAVE_TEAM', true);
    }

    public function test_user_leaves_without_having_a_member_entry_and_gets_404()
    {
        $user1 = User::factory()->create();
        
        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->post('/auth/team/leave-team', [
            'id' => $team->id,
        ]);
        
        $response->assertStatus(404);
        $response->assertSeeText('MEMBER_NOT_FOUND', true);
    }
    
    public function test_user_leaves_team_and_gets_teamid_back()
    {
        $user1 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        TeamMember::create([
            'roles' => ['member'],
            'team_id' => $team->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user2)->post('/auth/team/leave-team', [
            'id' => $team->id,
        ]);
        
        $response->assertSuccessful();
        $this->assertEquals($response->getContent(), $team->id);
    }
}

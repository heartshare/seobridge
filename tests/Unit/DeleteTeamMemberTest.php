<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTeamMemberTest extends TestCase
{
    use RefreshDatabase;
    
    // public function test_user_sends_empty_request_and_gets_error_on_id_and_memberid()
    // {
    //     $user = User::factory()->make();
    //     $response = $this->actingAs($user)->post('/auth/team/delete-member');
    //     $response->assertSessionHasErrors(['memberId', 'id']);
    // }

    public function test_none_owner_sends_request_and_gets_403()
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user->id,
        ]);

        $member = TeamMember::create([
            'roles' => ['member'],
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->post('/auth/team/delete-member', [
            'memberId' => $member->id,
            'id' => $team->id,
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('UNAUTHORIZED', true);
    }

    public function test_owner_sends_request_to_remove_self_and_gets_403()
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user->id,
        ]);

        $member = TeamMember::create([
            'roles' => ['member'],
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/auth/team/delete-member', [
            'memberId' => $member->id,
            'id' => $team->id,
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('CANNOT_DELETE_SELF', true);
    }

    public function test_user_sends_request_to_delete_user_from_another_team_and_gets_404()
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user->id,
        ]);

        $user2 = User::factory()->create();

        $team2 = Team::factory()->create([
            'owner_id' => $user->id,
        ]);

        $member2 = TeamMember::create([
            'roles' => ['member'],
            'team_id' => $team2->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user2)->post('/auth/team/delete-member', [
            'memberId' => $member2->id,
            'id' => $team->id,
        ]);
        
        $response->assertStatus(404);
        $response->assertSeeText('MEMBER_NOT_FOUND', true);
    }


    public function test_user_sends_request_to_delete_owner_and_gets_403()
    {
        $user1 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        $member2 = TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/delete-member', [
            'memberId' => $member2->id,
            'id' => $team->id,
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('CANNOT_DELETE_OWNER', true);
    }

    public function test_user_deletes_member_and_gets_memberid_back()
    {
        $user1 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        $member2 = TeamMember::create([
            'roles' => ['member'],
            'team_id' => $team->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/delete-member', [
            'memberId' => $member2->id,
            'id' => $team->id,
        ]);
        
        $response->assertSuccessful();
        $this->assertEquals($response->getContent(), $member2->id);
    }
}

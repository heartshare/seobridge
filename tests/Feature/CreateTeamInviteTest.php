<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTeamInviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_none_owner_sends_request_and_gets_403_with_text_response()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->make();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team->id,
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user2)->post('/auth/team/create-invite', [
            'teamId' => $team->id,
            'inviteName' => 'test@example.com',
        ]);
        
        $response->assertStatus(403);
        $response->assertSeeText('UNAUTHORIZED', true);
    }

    public function test_owner_sends_request_with_invalid_email_as_invitename_and_gets_error_with_invitename()
    {
        $user1 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team->id,
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/create-invite', [
            'teamId' => $team->id,
            'inviteName' => 'test-at-example.com', // Intentionally invalid Email
        ]);

        $response->assertSessionHasErrors(['inviteName']);
    }

    public function test_owner_sends_request_with_valid_email_as_invitename()
    {
        $exampleEmail = 'test@example.com';

        $user1 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team->id,
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/create-invite', [
            'teamId' => $team->id,
            'inviteName' => $exampleEmail,
        ]);
        
        $response->assertSuccessful();
        $this->assertEquals($exampleEmail, json_decode($response->getContent())->email);
    }

    public function test_owner_sends_request_with_userid_as_invitename()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $team = Team::factory()->create([
            'owner_id' => $user1->id,
        ]);

        TeamMember::create([
            'roles' => ['owner'],
            'team_id' => $team->id,
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user1)->post('/auth/team/create-invite', [
            'teamId' => $team->id,
            'inviteName' => $user2->id,
        ]);
        
        $response->assertSuccessful();
        $this->assertEquals($user2->id, json_decode($response->getContent())->user_id);
    }
}

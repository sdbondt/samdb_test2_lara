<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_project_owner_can_invite_a_user() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();
        $this->signIn($project->user);
        $userToInvite = User::factory()->create();

        $this->postJson(route('invitations.store', $project), [
            'email' => $userToInvite->email
        ]);
        $this->assertTrue($project->members->contains($userToInvite));
    }

    /** @test */
    function only_owners_may_invite_users() {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $this->signIn($user);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->postJson(route('invitations.store', $project));
    }

    /** @test */
    function invited_email_must_be_a_valid_user() {
        $project = Project::factory()->create();
        $this->signIn($project->user);

        $this->postJson(route('invitations.store', $project), [
            'email' => 'invalid@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    function invited_users_can_update_a_project() {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->invite($user);

        $this->signIn($user);
        $this->postJson(route('tasks.store', $project), ['body' => 'testing']);
        $this->assertDatabaseHas('tasks', ['body' => 'testing']);
    }
}

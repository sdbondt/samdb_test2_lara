<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;
    /** @test */ 
    public function user_can_create_a_project() {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $project = Project::factory()->raw([
            'user_id' => $user->id,
        ]);
        $this->signIn($user);
        $this->post(route('projects.store'), $project);
        
        $this->assertDatabaseHas('projects', $project);
    }

    /** @test */
    public function a_user_can_update_their_project() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);

        $this->patchJson(route('projects.update', $project->id), [
            'notes' => 'updated',
            'title' => 'updated',
            'description' => 'updated'
        ]);

        $this->assertDatabaseHas('projects', [
            'notes' => 'updated',
            'title' => 'updated',
            'description' => 'updated'
        ]);
    }

    /** @test */
    public function guests_cannot_create_a_project() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->raw([
            'user_id' => null,
        ]);
        
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->postJson(route('projects.store'), $project);  
    }

    /** @test */ 
    public function a_project_requires_a_title() {
        $project = Project::factory()->make(['title' => '']);
        Sanctum::actingAs($project->user);
        $this->postJson(route('projects.store'), [
            'title' => $project->title,
            'description' => $project->description
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
        
    }

    /** @test */ 
    public function a_project_requires_a_description() {
        $project = Project::factory()->make(['description' => '']);
        Sanctum::actingAs($project->user);
        $this->postJson(route('projects.store'), [
            'title' => $project->title,
            'description' => $project->description
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['description']);
    }

    /** @test */
    public function a_user_can_view_their_project() {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id
        ]);
        Sanctum::actingAs($user);
        $res = $this->getJson(route('projects.show', $project->id))
            ->assertOk()
            ->json();

        $this->assertEquals($project->title, $res['title']);
    }

    /** @test */
    public function a_user_cannot_view_the_projects_of_others() {
        $this->withoutExceptionHandling();
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $userOne->id
        ]);
        Sanctum::actingAs($userTwo);
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $res = $this->getJson(route('projects.show', $project->id));
    }

    /** @test */
    public function a_user_cannot_update_the_projects_of_others() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create();

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->patchJson(route('projects.update', $project->id), [
            'notes' => 'updated'
        ]);
    }


    /** @test */
    public function guests_cannot_view_a_project() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $res = $this->getJson(route('projects.show', $project->id));
    }

    /** @test */
    public function guests_cannot_view_projects() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('projects.index'));
    }

    /** @test */
    public function a_user_can_delete_their_project() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);
        $this->deleteJson(route('projects.destroy', $project->id));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /** @test */
    public function guests_cannot_delete_projects() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();

        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->deleteJson(route('projects.destroy', $project->id));
    }

    /** @test */
    public function another_user_cannot_delete_projects() {
        $this->withoutExceptionHandling();
        $user= User::factory()->create();
        $project = Project::factory()->create();
        Sanctum::actingAs($user);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->deleteJson(route('projects.destroy', $project->id));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */ 
    public function a_project_can_have_tasks() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);

        $this->postJson(route('tasks.store', $project->id), [
            'body' => 'testing'
        ]);
        $this->assertDatabaseHas('tasks', ['body' => 'testing']);
    }

    /** @test */ 
    public function a_task_needs_a_body() {
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);

         $this->postJson(route('tasks.store', $project->id))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    /** @test */
    public function guests_cannot_add_tasks() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->postJson(route('tasks.store', $project->id), [
            'body' => 'testing'
        ]); 
    }

    /** @test */
    public function only_owner_can_add_tasks_to_a_project() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create();

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->postJson(route('tasks.store', $project->id), [
            'body' => 'testing'
        ]); 
    }

    /** @test */
    public function a_task_can_be_updated() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);
        $task = $project->addTask('testing');
        $this->patchJson(route('tasks.update', [$project->id, $task->id]), [
            'body' => 'updating'
        ]);
        $this->assertDatabaseHas('tasks', ['body' => 'updating']);
    }

    /** @test */
    public function only_the_owner_of_a_project_can_update_tasks() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create();
        $task = $project->addTask('testing');

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->patchJson(route('tasks.update', [$project->id, $task->id]), [
            'body' => 'updating'
        ]);
    }
}

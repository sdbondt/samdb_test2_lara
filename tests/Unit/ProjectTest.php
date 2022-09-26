<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_belongs_to_an_user() {
        $project = Project::factory()->create();
        $this->assertInstanceOf(User::class, $project->user);
    }

    /** @test */
    public function a_project_can_add_tasks() {
        $project = Project::factory()->create();
        $task = $project->addTask('Testing');

        $this->assertCount(1, $project->tasks);
        $this->assertTrue($project->tasks->contains($task));
    }

    /** @test */
    public function a_project_can_invite_a_user() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->invite($user);

        $this->assertTrue($project->members->contains($user));
    }
}

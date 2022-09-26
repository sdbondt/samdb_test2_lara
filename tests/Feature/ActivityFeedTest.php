<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_project_generates_activity() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();

        $this->assertCount(1, $project->activity);
        $this->assertEquals('created_project', $project->activity->last()->description);
        $this->assertNull($project->activity->last()->changes);
    }

    /** @test */
    public function updating_a_project_generates_activity() {
        $this->withoutExceptionHandling();
        $project = Project::factory()->create();
        $originalTitle = $project->title;
        $project->update([
            'title' => 'updated'
        ]);

        $expected = [
            'before' => ['title' => $originalTitle],
            'after' => ['title' => 'updated']
        ];

        $this->assertCount(2, $project->activity);
        $this->assertEquals('updated_project', $project->activity->last()->description);
        $this->assertEquals($expected, $project->activity->last()->changes);
    }

    /** @test */
    public function creating_a_task_records_activity() {
        $project = Project::factory()->create();
        $project->addTask('task');

        $this->assertCount(2, $project->activity);
        $this->assertEquals('created_task', $project->activity->last()->description);
        $this->assertInstanceOf(Task::class, $project->activity->last()->subject);
    }

    /** @test */
    public function updating_a_task_records_activity() {
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);
        $task = $project->addTask('task');
        $this->patchJson(route('tasks.update', [$project->id, $task->id]), [
            'body' => 'updating'
        ]);

        $this->assertCount(3, $project->activity);
        $this->assertEquals('updated_task', $project->activity->last()->description);
    }

    /** @test */
    public function deleting_a_task_records_activity() {
        $this->signIn();
        $project = Project::factory()->create([
            'user_id' => auth()->user()->id
        ]);
        $task = $project->addTask('testing');
        $task->delete();

        $this->assertCount(3, $project->activity);
        $this->assertEquals('deleted_task', $project->activity->last()->description);
    }
}

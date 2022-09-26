<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_belongs_to_a_project() {
        $task = Task::factory()->create();
        $this->assertInstanceOf(Project::class, $task->project);
    }
}

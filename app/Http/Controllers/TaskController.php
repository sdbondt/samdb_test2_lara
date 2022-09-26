<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;

class TaskController extends Controller
{
    public function store(Project $project) {
        $this->authorize('update', [Project::class, $project]);
        $attr = request()->validate([
            'body' => 'required'
        ]);
        $task = $project->addTask($attr['body']);
        return $task;
    }

    public function update(Project $project, Task $task) {
        $this->authorize('view', [Project::class, $project]);
        $attr = request()->validate([
            'body' => 'sometimes',
            'completed' => 'sometimes'
        ]);
        $task->update($attr);
        return $task;
    }
}

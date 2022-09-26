<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function store() {
        $attr = request()->validate([
            'title' => ['required'],
            'description' => ['required'],
            'notes' => ['sometimes', 'max:255']
        ]);
        request()->user()->projects()->create($attr);
    }

    public function show(Project $project) {
        $this->authorize('view', [Project::class, $project]);
        return $project;
    }

    public function index() {
        return request()->user()->projects;
    }

    public function update(Project $project) {
        $this->authorize('view', [Project::class, $project]);
        $attr = request()->validate([
            'title' => 'sometimes',
            'description' => 'sometimes',
            'notes' => ['sometimes', 'max:255']
        ]);
        $project->update($attr);
        return $project;
    }

    public function destroy(Project $project) {
        $this->authorize('manage', [Project::class, $project]);
        $project->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

class InvitationsController extends Controller
{
    public function store(Project $project) {
        $this->authorize('manage', [Project::class, $project]);
        $attr = request()->validate([
            'email' => ['required','exists:users,email']
        ]);

        $user = User::whereEmail($attr['email'])->first();

        $project->invite($user);
    }
}

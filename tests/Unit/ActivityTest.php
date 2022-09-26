<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function activity_has_a_user() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = Project::factory()->create();

        $this->assertInstanceOf(User::class, $project->activity->first()->user);
    }
}

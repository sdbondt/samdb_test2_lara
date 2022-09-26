<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_projects() {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    /** @test */
    public function a_user_can_see_all_projects() {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $projectOne = Project::factory()->create([
            'user_id' => $userOne->id
        ]);
        $projectTwo = Project::factory()->create([
            'user_id' => $userTwo->id
        ]);
        $projectTwo->invite($userOne);

        $this->assertCount(2, $userOne->allProjects());
    }
}

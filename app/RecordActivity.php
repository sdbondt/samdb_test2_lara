<?php


namespace App;

use App\Models\Activity;
use Illuminate\Support\Arr;

trait RecordActivity {

    public $old = [];

    public function recordActivity($description) {
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => class_basename($this) == 'Project' ? $this->id: $this->project->id,
            'user_id' => $this->activityId()
        ]);
        
    }

    protected function activityId() {
        return ( $this->project ?? $this)->user->id;
    }

    protected function activityChanges() {
        if($this->wasChanged()) {
            return [
                'before' => Arr::except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
                'after' => Arr::except( $this->getChanges(), 'updated_at'),                
            ];
        }
    }

    public function activity() {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }
}
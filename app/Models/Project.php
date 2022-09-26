<?php

namespace App\Models;

use App\RecordActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    use RecordActivity;

    protected $fillable = ['title', 'description', 'notes'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function activity() {
        return $this->hasMany(Activity::class);
    }

    public function addTask($body) {
        return $this->tasks()->create(['body' => $body]);
    }

    public function invite(User $user) {
        $this->members()->attach($user);
    }
    
    public function members() {
        return $this->belongsToMany(User::class, 'project_members');
    }
}

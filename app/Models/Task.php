<?php

namespace App\Models;

use App\RecordActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    use RecordActivity;

    protected $fillable = ['body'];
    protected $touches = ['project'];
    protected $casts = [
        'completed' => 'boolean'
    ];
    // protected static function boot() {
    //     parent::boot();
    //     static::created(function($task) {
    //         $task->recordActivity('created_task');
    //     });

    //     static::updated(function($task) {
    //         $task->recordActivity('updated_task');
    //     });

    //     static::deleted(function ($task) {
    //         $task->recordActivity('deleted_task');
    //     });
    // }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    
}

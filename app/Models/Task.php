<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaskComment;
use App\Models\User;
class Task extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'claimed_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }
    public function claimedByUser()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
}
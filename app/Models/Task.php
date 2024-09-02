<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'user_id', 'title', 'description', 'dead_line', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusInfoAttribute()
    {
        $statusMap = [
            1 => 'Created',
            2 => 'In Progress',
            3 => 'Completed'
        ];

        return $statusMap[$this->attributes['status']] ?? 'Unknown';
    }
}

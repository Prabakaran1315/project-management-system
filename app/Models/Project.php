<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'start_date', 'end_date'];

    protected $dates = ['start_date', 'end_date', 'deleted_at'];

    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }
}

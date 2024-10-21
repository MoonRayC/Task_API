<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'priority_level',
        'status',
        'start_time',
        'end_time',
    ];
    
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

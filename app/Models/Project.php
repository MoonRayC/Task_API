<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'status',
        'start_time',
        'end_time',
    ];
    
    use HasFactory;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

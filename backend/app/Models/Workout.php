<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_of_week'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class, 'workout_id');
    }
}

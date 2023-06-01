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

    /**
     * Get the assigned exercises for this workout.
     */
    // public function workoutExercises()
    // {
    //     return $this->hasMany(WorkoutExercise::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class, 'workout_id');
    }
}

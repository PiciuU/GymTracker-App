<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'thumbnail_url',
        'attachment_url',
        'user_id',
        'is_public',
        'is_approved'
    ];

    /**
     * Get the assigned workouts for this exercise.
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
        return $this->hasMany(WorkoutExercise::class, 'exercise_id');
    }

    public function userExerciseHistory()
    {
        return $this->hasMany(UserExerciseHistory::class, 'exercise_id');
    }
}

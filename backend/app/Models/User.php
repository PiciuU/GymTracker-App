<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_role_id',
        'login',
        'email_verified_at',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the workouts associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function isAdmin() {
        return $this->userRole->name === 'Admin';
    }

    public function hasAdminPrivileges() {
        return $this->tokenCan('admin') && $this->userRole->name === 'Admin';
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class, 'user_id');
    }

    public function history()
    {
        return $this->hasMany(UserExerciseHistory::class, 'user_id');
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'user_id');
    }
}

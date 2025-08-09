<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsActivity, CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be logged for the admin.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'status', 'role'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('admin')
            ->setDescriptionForEvent(function(string $eventName) {
                return "Admin user has been {$eventName}";
            });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
        'formatted_role',
    ];

    /**
     * The guard that should be used for this model.
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * Get the URL to the admin's avatar.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!empty($attributes['avatar'])) {
                    return asset('storage/' . $attributes['avatar']);
                }
                
                // Default avatar based on the admin's name
                $name = $attributes['name'] ?? 'Admin';
                $initials = implode('', array_map(function($n) { 
                    return strtoupper($n[0]); 
                }, explode(' ', $name)));
                
                return 'https://ui-avatars.com/api/?name='.urlencode($initials).'&color=7F9CF5&background=EBF4FF';
            },
        );
    }

    /**
     * Check if the admin has a specific role.
     *
     * @param  string|array  $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        return in_array($this->role, $roles, true);
    }

    /**
     * Check if the admin is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if the admin is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the formatted role name.
     *
     * @return string
     */
    public function getFormattedRoleAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'editor' => 'Editor',
            'viewer' => 'Viewer',
            default => ucfirst(str_replace('_', ' ', $this->role))
        };
    }
}

<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lunar\Base\Traits\LunarUser;
use Lunar\Base\LunarUser as LunarUserInterface;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements LunarUserInterface, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LunarUser;

    use HasRoles {
        hasPermissionTo as parentHasPermissionTo;
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // If it's the admin panel, restrict access to staff emails defined in Lunar's staff table
        if ($panel->getId() === 'admin') {
            return \Illuminate\Support\Facades\DB::table('lunar_staff')
                ->where('email', $this->email)
                ->exists();
        }

        return true;
    }

    /**
     * Override hasPermissionTo to catch Spatie's exception if a permission doesn't exist for the 'web' guard.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        try {
            return $this->parentHasPermissionTo($permission, $guardName);
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

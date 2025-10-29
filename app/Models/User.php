<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rupadana\ApiService\Models\Token;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, HasSuperAdmin, TwoFactorAuthenticatable, Cachable;

    protected array $guard_name = ['api', 'web'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    protected $fillable = [
        'type',
        'name',
        'email',
        'password',
        'company_name',
        'phone',
        'inn',
        'kpp',
        'bik',
        'correspondent_account',
        'bank_account',
        'yur_address',
    ];

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

    /**
     * Check if the user has a specific token permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasTokenPermission($ability, $user)
    {

        if ($user->hasRole('super_admin')) return true;
        $can_handle = false;
        $tokens = Token::where('tokenable_id', $this->id)->get();
        foreach ($tokens as $token) {
            $can_handle = in_array($ability, $token->abilities);
        }
        return $can_handle;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->getAllPermissions()->count() > 0;
    }
}

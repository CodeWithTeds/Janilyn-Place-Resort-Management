<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Models\HousekeepingTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'role',
        'password',
        'loyalty_points',
        'loyalty_tier',
        'guest_notes',
    ];

    /**
     * Check if the user has the given role.
     */
    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if the user is an owner.
     */
    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    /**
     * Check if the user is a staff member.
     */
    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'role' => UserRole::class,
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function rewards()
    {
        return $this->hasMany(UserReward::class);
    }

    public function addPoints(int $amount, string $description = null, $reference = null)
    {
        $this->increment('loyalty_points', $amount);
        
        $this->loyaltyTransactions()->create([
            'points' => $amount,
            'type' => 'earned',
            'description' => $description,
            'reference_id' => $reference ? $reference->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
        
        $this->checkTierUpgrade();
    }

    public function redeemPoints(int $amount, string $description = null, $reference = null)
    {
        if ($this->loyalty_points < $amount) {
            return false;
        }

        $this->decrement('loyalty_points', $amount);
        
        $this->loyaltyTransactions()->create([
            'points' => -$amount,
            'type' => 'redeemed',
            'description' => $description,
            'reference_id' => $reference ? $reference->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);

        return true;
    }

    public function checkTierUpgrade()
    {
        if ($this->loyalty_points >= 1000) {
            $this->update(['loyalty_tier' => 'Gold']);
        } elseif ($this->loyalty_points >= 500) {
            $this->update(['loyalty_tier' => 'Silver']);
        } else {
            $this->update(['loyalty_tier' => 'Bronze']);
        }
    }

    /**
     * Get the housekeeping tasks assigned to the user.
     */
    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'assigned_to');
    }

    /**
     * Scope a query to only include staff users.
     */
    public function scopeStaff($query)
    {
        return $query->where('role', UserRole::STAFF);
    }
}

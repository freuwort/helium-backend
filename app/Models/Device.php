<?php

namespace App\Models;

use App\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Device extends Model
{
    use HasApiTokens, HasFactory, HasAddresses;

    protected $fillable = [
        'name',
        'type',
        'pin',
        'os_platform',
        'os_arch',
        'os_release',
        'app_version',
    ];

    protected $casts = [
        'pin_generated_at' => 'datetime',
    ];

    protected $hidden = [
        'pin',
    ];

    const TYPES = [
        // 'POS',
        'KIOSK',
    ];

    const PIN_VALID_MINUTES = 10;
    const PIN_REGENERATE_MINUTES = 5;



    public static function booted()
    {
        static::creating(function ($device) {
            $device->pin = self::newPin();
            $device->pin_generated_at = now();
        });
    }



    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }



    public function scopeWhereActive($query) {
        return $query->whereNotNull('type');
    }

    public function scopeWherePending($query) {
        return $query->whereNull('type');
    }



    public function getValidUntilAttribute()
    {
        return $this->pin_generated_at->addMinutes(self::PIN_VALID_MINUTES);
    }



    public function shouldRegeneratePin(): bool {
        return $this->pin_generated_at->diffInMinutes() > self::PIN_REGENERATE_MINUTES;
    }

    public function mustRegeneratePin(): bool {
        return $this->pin_generated_at->diffInMinutes() > self::PIN_VALID_MINUTES;
    }
    
    public static function newPin() {
        // 8 pin number including leading zeros
        return str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    public function regeneratePin() {
        $this->update([ 'pin' => self::newPin(), 'pin_generated_at' => now(), ]);
    }

    public function validatePin($pin): bool {
        return $this->pin === $pin && !$this->mustRegeneratePin();
    }

    public function activateAs(array $deviceInfo) {
        if ($this->type) {
            throw new \LogicException('Device is already activated');
        }

        if (!in_array($deviceInfo['type'], self::TYPES)) {
            throw new \InvalidArgumentException('Invalid device type');
        }
        
        $this->update([
            'type' => $deviceInfo['type'],
            'name' => $deviceInfo['name'],
            'os_platform' => $deviceInfo['os_platform'] ?? null,
            'os_arch' => $deviceInfo['os_arch'] ?? null,
            'os_release' => $deviceInfo['os_release'] ?? null,
            'app_version' => $deviceInfo['app_version'] ?? null,
        ]);

        return $this->createToken($this->name);
    }
}

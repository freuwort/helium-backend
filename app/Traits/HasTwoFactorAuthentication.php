<?php

namespace App\Traits;

use App\Models\TwoFactorBackupCode;
use App\Models\TwoFactorMethod;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use OTPHP\TOTP;

trait HasTwoFactorAuthentication
{
    // START: General methods
    public function twoFactorMethods(): MorphMany
    {
        return $this->morphMany(TwoFactorMethod::class, 'authenticatable');
    }

    public function twoFactorBackupCodes(): MorphMany
    {
        return $this->morphMany(TwoFactorBackupCode::class, 'authenticatable');
    }


    public function getHasTfaEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('enabled', true)->exists();
    }

    public function getHasTfaBackupCodesAttribute(): Bool
    {
        return $this->twoFactorBackupCodes()->count() > 0;
    }


    public function getDefaultTfaMethodAttribute(): TwoFactorMethod|null
    {
        return $this->twoFactorMethods()->firstWhere('default', true);
    }

    public function setDefaultTfaMethod(string $method): void
    {
        $this->twoFactorMethods()->where('default', true)->update([
            'default' => false,
        ]);

        $this->twoFactorMethods()->where('type', $method)->update([
            'default' => true,
        ]);
    }


    public function destroyTfaMethod(string $method): Bool
    {
        return $this->twoFactorMethods()->where('type', $method)->delete();
    }
    // END: General methods



    // START: TOTP methods
    public function getTfaTotpMethodAttribute(): TwoFactorMethod|null
    {
        return $this->twoFactorMethods()->firstWhere('type', 'totp');
    }
    
    public function getHasTfaTotpMethodEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('type', 'totp')->where('enabled', true)->exists();
    }


    public function setupTfaTotp(): void
    {
        // If TOTP is already enabled, do nothing
        if($this->twoFactorMethods()->where('type', 'totp')->where('enabled', true)->exists()) return;

        // If TOTP is not enabled, create a new TOTP method or update the existing one
        $this->twoFactorMethods()->updateOrCreate([
            'type' => 'totp',
        ],[
            'recipient' => 'Authenticator App',
            'secret' => TOTP::generate()->getSecret(),
        ]);
    }

    public function TfaTotpQrCode(string $label = '', int $size = 200): string
    {
        // Get the TOTP method
        $method = $this->tfa_totp_method;

        // Check if Method exists
        if (!$method) throw new \Exception('TOTP method not found');

        // Check if TOTP is already enabled
        if ($method->enabled) throw new \Exception('TOTP is already enabled');

        // Create OTP object
        $otp = TOTP::createFromSecret($method->secret);
        $otp->setLabel($label);
        
        // Generate the QR code
        return Builder::create()
        ->writer(new PngWriter())
        ->data($otp->getProvisioningUri())
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size($size)
        ->margin(8)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->validateResult(false)
        ->build()
        ->getDataUri();
    }

    public function enableTfaTotp($otp): TwoFactorMethod|null
    {
        // Get the TOTP method
        $method = $this->tfa_totp_method;

        // Check if Method exists
        if (!$method) return null;

        // Check if Method is already enabled
        if ($method->enabled) return $method;

        // Check if OTP is valid
        if (!TOTP::createFromSecret($method->secret)->verify((string) $otp)) return null;
        
        // Enable the method
        $method->update([ 'enabled' => true ]);

        return $method;
    }

    public function verifyTfaTotp($otp): Bool
    {
        // Get the TOTP method
        $method = $this->tfa_totp_method;

        // Check if Method exists
        if (!$method) return false;

        // Check if Method is enabled
        if (!$method->enabled) return false;

        // Check if OTP is valid
        return TOTP::createFromSecret($method->secret)->verify($otp);
    }
    // END: TOTP methods
    


    // START: SMS methods
    public function getTfaSmsMethodAttribute(): TwoFactorMethod|null
    {
        return $this->twoFactorMethods()->firstWhere('type', 'sms');
    }
    
    public function getHasTfaSmsMethodEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('type', 'sms')->where('enabled', true)->exists();
    }
    // END: SMS methods



    // START: Email methods
    public function getTfaEmailMethodAttribute(): TwoFactorMethod|null
    {
        return $this->twoFactorMethods()->firstWhere('type', 'email');
    }

    public function getHasTfaEmailMethodEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('type', 'email')->where('enabled', true)->exists();
    }
    // END: Email methods



    // START: Backup codes
    public function generateTfaBackupCodes(): void
    {
        $this->twoFactorBackupCodes()->delete();

        for ($i = 0; $i < 10; $i++)
        {
            $this->twoFactorBackupCodes()->create([ 'code' => Str::random(8) ]);
        }
    }

    public function verifyTfaBackupCode($code): Bool
    {
        $code = $this->twoFactorBackupCodes()->firstWhere('code', $code);

        if (!$code) return false;

        $code->delete();

        return true;
    }
    // END: Backup codes
}
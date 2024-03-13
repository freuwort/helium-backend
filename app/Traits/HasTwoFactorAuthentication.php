<?php

namespace App\Traits;

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

    public function getHasTwoFactorEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('enabled', true)->exists();
    }

    public function defaultTwoFactorMethod(): TwoFactorMethod|null
    {
        return $this->twoFactorMethods()->firstWhere('default', true);
    }

    public function setDefaultTwoFactorMethod(string $method): Bool
    {
        // Exit if method is backup
        if ($method === 'backup') return false;

        $this->twoFactorMethods()->where('default', true)->update([
            'default' => false,
        ]);

        $this->twoFactorMethods()->where('type', $method)->update([
            'default' => true,
        ]);

        return true;
    }

    public function destroyTwoFactorMethod(string $method): Bool
    {
        return $this->twoFactorMethods()->where('type', $method)->delete();
    }
    // END: General methods



    // START: TOTP methods
    public function TfaTotpMethod(): TwoFactorMethod
    {
        return $this->twoFactorMethods()->where('type', 'totp')->firstOrFail();
    }
    
    public function getHasTfaTotpEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('type', 'totp')->where('enabled', true)->exists();
    }

    public function setupTotp(): void
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

    public function TotpProvisioningQrCode(string $label = '', int $size = 200): string
    {
        // Get the TOTP method
        $method = $this->TfaTotpMethod();

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

    public function enableTotp($otp): Bool
    {
        // Get the TOTP method
        $method = $this->TfaTotpMethod();

        // Check if Method is already enabled
        if ($method->enabled) return true;

        // Check if OTP is valid
        if (!TOTP::createFromSecret($method->secret)->verify((string) $otp)) return false;
        
        // Enable the method
        $method->update([ 'enabled' => true ]);

        return true;
    }

    public function verifyTotp($otp): Bool
    {
        // Get the TOTP method
        $method = $this->TfaTotpMethod();

        // Check if Method is enabled
        if (!$method->enabled) return false;

        // Check if OTP is valid
        return TOTP::createFromSecret($method->secret)->verify($otp);
    }
    // END: TOTP methods
    


    // START: SMS methods
    public function TfaSmsMethod(): TwoFactorMethod
    {
        return $this->twoFactorMethods()->where('type', 'sms')->firstOrFail();
    }
    
    public function getHasTfaSmsEnabledAttribute(): Bool
    {
        return $this->twoFactorMethods()->where('type', 'sms')->where('enabled', true)->exists();
    }
    // END: SMS methods



    // START: Email methods
    // END: Email methods



    // START: Backup codes
    public function backupCodes(): TwoFactorMethod
    {
        return $this->twoFactorMethods()->firstWhere('type', 'backup');
    }

    public function setBackupCodes(): void
    {
        $this->twoFactorMethods()->updateOrCreate([
            'type' => 'backup',
        ],[
            'backup_codes' => array_map(fn() => Str::random(8), range(0, 9)),
            'enabled' => true,
        ]);
    }

    public function verifyBackupCode($code): Bool
    {
        // Get the backup codes
        $codes = $this->backupCodes()->backup_codes;

        // Check if the code is valid
        if (!in_array($code, $codes)) return false;

        // Remove the code from the list
        $codes = array_diff($codes, [$code]);

        // Update the backup codes
        $this->backupCodes()->update([
            'backup_codes' => $codes,
        ]);

        return true;
    }
    // END: Backup codes
}
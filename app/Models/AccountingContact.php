<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'vat_id',
        'tax_id',
        'customer_id',
        'supplier_id',
        'employee_id',
        'contact_person',
        'version',
    ];



    public function sync()
    {
        return $this->belongsTo(User::class, 'sync_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function main_address()
    {
        return $this->belongsTo(Address::class, 'main_address_id');
    }

    public function billing_address()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shipping_address()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }
}

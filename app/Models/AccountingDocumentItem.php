<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingDocumentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'quantity',
        'unit',
        'tax_rate',
        'price_net',
        'price_gross',
        'price_tax',
    ];

    public function accountingDocument()
    {
        return $this->belongsTo(AccountingDocument::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
